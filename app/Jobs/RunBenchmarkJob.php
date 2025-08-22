<?php

namespace App\Jobs;

use App\Models\BenchmarkResult;
use App\Models\SourceServer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Foundation\Bus\Dispatchable;
use phpseclib3\Net\SSH2;

class RunBenchmarkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300; // detik

    public function __construct(public int $benchmarkResultId) {}

    public function handle(): void
    {
        $result = BenchmarkResult::find($this->benchmarkResultId);
        if (!$result) return;

        $server = SourceServer::find($result->source_server_id);
        if (!$server) {
            $result->update(['status' => 'failed', 'raw_output' => 'Source server not found.']);
            return;
        }

        $proto = $result->protocol;
        $count = (int) $result->packet_count;
        $size  = (int) $result->packet_size;
        $ival  = (int) $result->interval_us;
        $port  = $result->port;
        $dst   = $result->target_ip;

        $cmd = match($proto) {
            'TCP'  => "sudo hping3 -S -c {$count} -i u{$ival} -d {$size} -p {$port} {$dst}",
            'UDP'  => "sudo hping3 -2 -c {$count} -i u{$ival} -d {$size} -p {$port} {$dst}",
            'ICMP' => "sudo hping3 -1 -c {$count} -i u{$ival} -d {$size} {$dst}",
        };

        $ssh = new SSH2($server->ip, $server->ssh_port);
        $started = microtime(true);

        if (!$ssh->login($server->ssh_user, $server->ssh_password)) {
            $result->update(['status'=>'failed','raw_output'=>'SSH login failed']);
            return;
        }

        $ssh->setTimeout(180);
        $output = $ssh->exec($cmd);
        $ended  = microtime(true);
        $duration = max(0.001, $ended - $started);

        $sent = null; $rcvd = null;
        if (preg_match('/sent\s+(\d+)/i', $output, $m)) { $sent = (int)$m[1]; }
        if (preg_match('/rcvd\s+(\d+)/i', $output, $m)) { $rcvd = (int)$m[1]; }

        $packetsSent = $sent ?? $count;
        $pps = $packetsSent / $duration;
        $overhead = $proto === 'TCP' ? 40 : 28;
        $bytesPerPacket = $size + $overhead;
        $bitrate = (int) round($pps * $bytesPerPacket * 8);

        $result->update([
            'duration_sec'     => $duration,
            'packets_sent'     => $packetsSent,
            'packets_received' => $rcvd,
            'pps'              => $pps,
            'bitrate_bps'      => $bitrate,
            'raw_output'       => $output,
            'status'           => 'finished',
        ]);
    }
}
