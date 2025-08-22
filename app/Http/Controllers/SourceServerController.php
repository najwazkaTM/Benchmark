<?php

namespace App\Http\Controllers;

use App\Models\SourceServer;
use Illuminate\Http\Request;
use phpseclib3\Net\SSH2;

class SourceServerController extends Controller
{
    public function index()
    {
        $servers = SourceServer::latest()->get();
        return view('servers.index', compact('servers'));
    }

    public function create()
    {
        return view('servers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:190',
            'ip'            => 'required|ip',
            'ssh_port'      => 'required|integer|min:1|max:65535',
            'ssh_user'      => 'required|string|max:190',
            'ssh_password'  => 'required|string|max:255',
            'iface'         => 'nullable|string|max:64',
            'hping_path'    => 'required|string|max:255',

            // Per-protocol options (boleh kosong; divalidasi saat Run)
            'hping_tcp_options'  => ['nullable','string','max:255','not_regex:/(^|\s)--flood(\s|$)/i'],
            'hping_udp_options'  => ['nullable','string','max:255','not_regex:/(^|\s)--flood(\s|$)/i'],
            'hping_icmp_options' => ['nullable','string','max:255','not_regex:/(^|\s)--flood(\s|$)/i'],
        ], [
            'hping_tcp_options.not_regex'  => 'TCP options tidak boleh mengandung --flood.',
            'hping_udp_options.not_regex'  => 'UDP options tidak boleh mengandung --flood.',
            'hping_icmp_options.not_regex' => 'ICMP options tidak boleh mengandung --flood.',
        ]);

        SourceServer::create($data);

        return redirect()->route('servers.index')->with('success', 'Server added.');
    }

    public function edit(SourceServer $server)
    {
        return view('servers.edit', compact('server'));
    }

    public function update(Request $request, SourceServer $server)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:190',
            'ip'            => 'required|ip',
            'ssh_port'      => 'required|integer|min:1|max:65535',
            'ssh_user'      => 'required|string|max:190',
            'ssh_password'  => 'nullable|string|max:255',
            'iface'         => 'nullable|string|max:64',
            'hping_path'    => 'required|string|max:255',

            'hping_tcp_options'  => ['nullable','string','max:255','not_regex:/(^|\s)--flood(\s|$)/i'],
            'hping_udp_options'  => ['nullable','string','max:255','not_regex:/(^|\s)--flood(\s|$)/i'],
            'hping_icmp_options' => ['nullable','string','max:255','not_regex:/(^|\s)--flood(\s|$)/i'],
        ], [
            'hping_tcp_options.not_regex'  => 'TCP options tidak boleh mengandung --flood.',
            'hping_udp_options.not_regex'  => 'UDP options tidak boleh mengandung --flood.',
            'hping_icmp_options.not_regex' => 'ICMP options tidak boleh mengandung --flood.',
        ]);

        // Jangan overwrite password jika kosong
        if (!isset($data['ssh_password']) || $data['ssh_password'] === '') {
            unset($data['ssh_password']);
        }

        $server->update($data);

        return redirect()->route('servers.index')->with('success', 'Server updated.');
    }

    public function destroy(SourceServer $server)
    {
        $server->delete();
        return redirect()->route('servers.index')->with('success', 'Server deleted.');
    }

    /** Cek cepat SSH */
    public function check(SourceServer $server)
    {
        try {
            $ssh = new SSH2($server->ip, $server->ssh_port, 5);
            $ssh->setTimeout(8);
            if (!$ssh->login($server->ssh_user, $server->ssh_password)) {
                $server->update(['ssh_status'=>'failed','last_checked_at'=>now()]);
                return back()->with('error', 'SSH failed (auth).');
            }
            $out = trim((string)$ssh->exec('echo ok'));
            $server->update([
                'ssh_status'      => $out === 'ok' ? 'connected' : 'failed',
                'last_checked_at' => now(),
            ]);
            return back()->with($out === 'ok' ? 'success' : 'error',
                $out === 'ok' ? 'SSH connected.' : 'SSH echo mismatch.');
        } catch (\Throwable $e) {
            $server->update(['ssh_status'=>'failed','last_checked_at'=>now()]);
            return back()->with('error', 'SSH error: '.$e->getMessage());
        }
    }

    public function checkAll()
    {
        $servers = SourceServer::all();
        $ok=0; $fail=0;
        foreach ($servers as $s) {
            try {
                $ssh = new SSH2($s->ip, $s->ssh_port, 5);
                $ssh->setTimeout(8);
                $status = ($ssh->login($s->ssh_user, $s->ssh_password) && trim((string)$ssh->exec('echo ok'))==='ok')
                    ? 'connected' : 'failed';
            } catch (\Throwable $e) { $status = 'failed'; }
            $s->update(['ssh_status'=>$status,'last_checked_at'=>now()]);
            $status==='connected' ? $ok++ : $fail++;
        }
        return back()->with('success', "Checked {$servers->count()} servers. Connected: {$ok}, Failed: {$fail}");
    }
}
