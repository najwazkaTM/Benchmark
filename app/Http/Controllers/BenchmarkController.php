<?php

namespace App\Http\Controllers;

use App\Models\BenchmarkResult;
use App\Models\SourceServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use phpseclib3\Net\SSH2;

class BenchmarkController extends Controller
{
    public function create()
    {
        $servers = SourceServer::orderBy('name')->get();
        return view('benchmarks.create', compact('servers'));
    }

    /** START benchmark: semua setting dari menu (per-protocol). */
    public function run(Request $request)
    {
        $data = $request->validate([
            'source_server_id' => 'required|exists:source_servers,id',
            'target_ip'        => 'required|ip',
            'protocol'         => 'required|in:TCP,UDP,ICMP',
            'port'             => 'nullable|integer|min:1|max:65535',
        ]);

        $server = SourceServer::findOrFail($data['source_server_id']);
        $dst    = $data['target_ip'];
        $proto  = $data['protocol'];
        $port   = $data['port'] ?? null;

        // Path wajib
        $hpingPath = trim((string)$server->hping_path);

        // Options per-protocol
        $options = match ($proto) {
            'TCP'  => trim((string)$server->hping_tcp_options),
            'UDP'  => trim((string)$server->hping_udp_options),
            'ICMP' => trim((string)$server->hping_icmp_options),
        };

        if ($hpingPath === '' || $options === '') {
            return back()->with('error', "Set 'hping3 path' dan opsi {$proto} di menu Source Server terlebih dahulu.");
        }

        // Validasi flag protokol
        $needFlag = match ($proto) {
            'TCP'  => '-S',
            'UDP'  => '-2',
            'ICMP' => '-1',
        };
        if (!preg_match('/(^|\s)'.preg_quote($needFlag,'/').'(\s|$)/', $options)) {
            return back()->with('error', "hping options {$proto} harus memuat flag protokol yang sesuai ({$needFlag}).");
        }

        // SSH & binary check
        $ssh = new SSH2($server->ip, $server->ssh_port);
        if (!$ssh->login($server->ssh_user, $server->ssh_password)) {
            return back()->with('error', 'SSH login gagal: '.$server->ip);
        }
        $ssh->setTimeout(6);
        $ok = trim($ssh->exec("test -x ".escapeshellarg($hpingPath)." && echo OK || echo ''"));
        if ($ok === '') {
            return back()->with('error', "Binary hping3 tidak ditemukan/eksekutabel di: {$hpingPath}");
        }

        // Tentukan iface egress (fallback ke menu)
        $iface = trim($ssh->exec("ip -o route get ".escapeshellarg($dst)." | awk '{for(i=1;i<=NF;i++) if(\$i==\"dev\"){print \$(i+1); exit}}'"));
        if ($iface === '') { $iface = $server->iface ?: ''; }
        if ($iface === '') {
            return back()->with('error', "Tidak bisa menentukan interface egress ke {$dst}. Set 'iface' di Source Server.");
        }

        // Tambahkan -p jika TCP/UDP & user isi port dan belum ada -p di options
        $finalOptions = $options;
        $hasPort = preg_match('/(^|\s)-p\s+\d+(\s|$)/', $finalOptions);
        if ($proto !== 'ICMP' && !$hasPort && $port) {
            $finalOptions .= " -p {$port}";
        }

        // Build command (target host WAJIB terakhir)
        $cmd = $hpingPath.' '.$finalOptions.' '.$dst;

        // Background
        $tag = 'bench-'.bin2hex(random_bytes(4));
        $log = "/tmp/{$tag}.log";
        $bg  = "nohup sh -c '".addslashes($cmd)."' > {$log} 2>&1 & echo $!";

        $ssh->setTimeout(10);
        $pid = trim($ssh->exec($bg));
        if (!ctype_digit($pid)) {
            $errTail = trim($ssh->exec("tail -n 120 {$log} 2>/dev/null"));
            return back()->with('error', "Gagal start (pid tidak valid). Log: ".$errTail);
        }

        usleep(300000);
        $alive = trim($ssh->exec("ps -o comm= -p {$pid} 2>/dev/null"));
        if ($alive === '') {
            $errTail = trim($ssh->exec("tail -n 200 {$log} 2>/dev/null"));
            return back()->with('error', "Proses langsung exit. Log:\n".$errTail);
        }

        // Ambil meta dari options untuk tabel
        $packetCount  = $this->extractInt($finalOptions, '/(^|\s)-c\s+(\d+)/');
        $intervalUs   = $this->extractInt($finalOptions, '/(^|\s)-i\s+u?(\d+)/');
        $packetSize   = $this->extractInt($finalOptions, '/(^|\s)-d\s+(\d+)/');

        $res = BenchmarkResult::create([
            'source_server_id' => $server->id,
            'target_ip'        => $dst,
            'protocol'         => $proto,
            'port'             => $port,
            'packet_count'     => $packetCount,
            'packet_size'      => $packetSize,
            'interval_us'      => $intervalUs,
            'duration_sec'     => null,
            'packets_sent'     => null,
            'packets_received' => null,
            'pps'              => null,
            'bitrate_bps'      => null,
            'raw_output'       => "running; log={$log}\ncmd: {$cmd}",
            'status'           => 'running',
            'remote_pid'       => (int)$pid,
            'started_at'       => now(),
            'iface_used'       => $iface,
        ]);

        return redirect()->route('benchmarks.show', $res)
            ->with('success', "Started. PID {$pid} via {$iface} ({$proto})");
    }

    /** STOP: SIGINT lalu SIGKILL; tail log ke raw_output; isi duration. */
    public function stop(BenchmarkResult $benchmark)
    {
        $benchmark->load('sourceServer');
        if ($benchmark->status !== 'running' || !$benchmark->remote_pid) {
            return back()->with('error', 'Benchmark tidak dalam keadaan running.');
        }

        $srv = $benchmark->sourceServer;
        $ssh = new SSH2($srv->ip, $srv->ssh_port);
        if (!$ssh->login($srv->ssh_user, $srv->ssh_password)) {
            return back()->with('error','SSH login failed.');
        }
        $ssh->setTimeout(5);

        $ssh->exec("kill -2 {$benchmark->remote_pid} 2>/dev/null || true");
        usleep(800000);
        $ssh->exec("kill -9 {$benchmark->remote_pid} 2>/dev/null || true");

        $log = null;
        if (preg_match('/log=(\/tmp\/[^\s]+)/', (string)$benchmark->raw_output, $m)) {
            $logPath = $m[1];
            $ssh->setTimeout(3);
            $log = $ssh->exec("tail -n 400 {$logPath} 2>/dev/null");
        }

        $duration = optional($benchmark->started_at)->diffInSeconds(now()) ?? null;

        $benchmark->update([
            'status'       => 'finished',
            'stopped_at'   => now(),
            'duration_sec' => $duration,
            'raw_output'   => $log ?? $benchmark->raw_output,
        ]);

        return redirect()->route('benchmarks.show', $benchmark)->with('success','Stopped.');
    }

    /** SSE realtime: delta TX iface setiap ~1s */
    public function stream(BenchmarkResult $benchmark)
    {
        $benchmark->load('sourceServer');
        $srv = $benchmark->sourceServer;
        $iface = $benchmark->iface_used ?: $srv->iface;
        if (!$iface) abort(422, 'Interface belum diset/dideteksi.');

        $ssh = new SSH2($srv->ip, $srv->ssh_port);
        if (!$ssh->login($srv->ssh_user, $srv->ssh_password)) abort(500, 'SSH login gagal.');
        $ssh->setTimeout(3);

        $readCounters = function() use ($ssh, $iface) {
            $cmd = "cat /proc/net/dev | awk '/^\\s*".$iface.":/ {print $10\" \"$11}'";
            $out = trim((string)$ssh->exec($cmd));
            if (!$out || !preg_match('/^\\d+\\s+\\d+$/', $out)) return [null, null];
            [$txBytes, $txPackets] = array_map('intval', explode(' ', $out));
            return [$txBytes, $txPackets];
        };

        return Response::stream(function () use ($readCounters, $benchmark) {
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('X-Accel-Buffering: no');

            [$prevB, $prevP] = $readCounters();
            $prevT = microtime(true);

            while (true) {
                $benchmark->refresh();
                if ($benchmark->status !== 'running') {
                    echo "event: done\n";
                    echo "data: {}\n\n";
                    ob_flush(); flush();
                    break;
                }

                usleep(1_000_000);
                [$curB, $curP] = $readCounters();
                $curT = microtime(true);

                if ($prevB !== null && $curB !== null) {
                    $dt  = max(0.001, $curT - $prevT);
                    $dB  = max(0, $curB - $prevB);
                    $dP  = max(0, $curP - $prevP);
                    $pps = $dP / $dt;
                    $bps = (int)round(($dB * 8) / $dt);

                    $payload = json_encode([
                        'ts'   => now()->format('H:i:s'),
                        'pps'  => round($pps, 2),
                        'mbps' => round($bps / 1_000_000, 2),
                    ]);
                    echo "data: {$payload}\n\n";
                    ob_flush(); flush();
                }

                $prevB = $curB; $prevP = $curP; $prevT = $curT;
            }
        });
    }

    private function extractInt(string $options, string $pattern): ?int
    {
        if (preg_match($pattern, $options, $m)) {
            return isset($m[2]) ? (int)$m[2] : null;
        }
        return null;
    }
}
