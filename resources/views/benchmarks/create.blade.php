<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Avalanche - Run Benchmark</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        :root {
            --primary-blue: #3b82f6;
            --light-blue: #dbeafe;
            --gray-40: #9ca3af;
        }
        
        .form-container {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }
        
        .input-field {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }
        
        .input-field:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-weight: 500;
        }
        
        .btn-primary {
            background-color: #3b82f6;
            color: white;
            border-radius: 0.5rem;
            padding: 0.625rem 1.25rem;
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover:not(:disabled) {
            background-color: #2563eb;
        }
        
        .btn-primary:disabled {
            background-color: #9ca3af;
            cursor: not-allowed;
        }
        
        .info-box {
            background-color: #eff6ff;
            border: 1px solid #dbeafe;
            border-radius: 0.5rem;
        }
        
        .server-status {
            background-color: #f8fafc;
            border-radius: 0.5rem;
            padding: 1rem;
        }
    </style>
</head>
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md h-screen fixed">
     @include('layouts.app')
    </aside>

    <!-- Konten -->
<main class="flex-1 flex justify-center ml-64 p-4 bg-gray-50 overflow-auto">
    <div class="p-4 max-w-l mx-auto">  
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-lg font-semibold text-gray-800">Run Benchmark</h1>
                <p class="text-xs text-gray-500">Test network performance between servers</p>
            </div>

            {{-- Error bag --}}
            @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red">Input error:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @php
                $serverMeta = $servers->map(fn($s) => [
                    'id'      => $s->id,
                    'name'    => $s->name,
                    'ip'      => $s->ip,
                    'iface'   => $s->iface,
                    'status'  => $s->ssh_status ?? 'unknown',
                    'checked' => optional($s->last_checked_at)->format('Y-m-d H:i:s') ?? '-',
                ]);
                $oldServerId = old('source_server_id');
                $oldProto    = old('protocol', 'TCP');
            @endphp

            <div
              x-data="{
                  servers: {{ $serverMeta->toJson() }},
                  selectedId: {{ json_encode($oldServerId) }},
                  proto: {{ json_encode($oldProto) }},
                  get selected() { return this.servers.find(s => s.id == this.selectedId) || {}; },
                  get statusClass() {
                      return this.selected.status === 'connected'
                          ? 'bg-green-100 text-green-800'
                          : (this.selected.status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800');
                  },
                  get portDisabled() { return this.proto === 'ICMP'; },
              }"
              class="max-w-3xl"
            >
                <div class="form-container p-6">
                    <form method="POST" action="{{ route('benchmarks.run') }}" class="space-y-6">
                        @csrf

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Source Server</label>
                                <select name="source_server_id" x-model="selectedId" class="w-full input-field py-2.5 px-3.5" required>
                                    <option value="">-- Select server --</option>
                                    @foreach ($servers as $s)
                                        <option value="{{ $s->id }}" @selected(old('source_server_id') == $s->id)>
                                            {{ $s->name }} ({{ $s->ip }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-2">Interface: <span x-text="selected.iface || '-'" class="font-medium"></span></p>
                            </div>

                            <div class="server-status">
                                <div class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-2">SSH Status</div>
                                <div class="flex items-center justify-between mb-2">
                                    <span x-text="(selected.name||'-') + ' (' + (selected.ip||'-') + ')'" class="text-sm font-medium"></span>
                                    <span class="status-badge" :class="statusClass"
                                          x-text="(selected.status||'unknown')[0].toUpperCase() + (selected.status||'unknown').slice(1)"></span>
                                </div>
                                <div class="text-xs text-gray-500">
                                    Last checked: <span x-text="selected.checked || '-'" class="font-medium"></span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Configure interface & protocol commands in
                                    <a class="text-blue-600 hover:text-blue-800" href="{{ route('servers.index') }}">Source Servers</a>.
                                </div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Target IP</label>
                                <input name="target_ip" value="{{ old('target_ip') }}" class="w-full input-field py-2.5 px-3.5" placeholder="e.g. 10.0.0.10" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Protocol</label>
                                <select name="protocol" x-model="proto" class="w-full input-field py-2.5 px-3.5" required>
                                    <option value="TCP">TCP</option>
                                    <option value="UDP">UDP</option>
                                    <option value="ICMP">ICMP</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Port (TCP/UDP)</label>
                                <input name="port"
                                       value="{{ old('port') }}"
                                       type="number" min="1" max="65535"
                                       class="w-full input-field py-2.5 px-3.5"
                                       :disabled="portDisabled"
                                       :placeholder="portDisabled ? 'N/A (ICMP)' : 'e.g. 80'">
                            </div>
                        </div>

                        <div class="info-box p-4">
                            <p class="text-xs text-gray-700">
                                Protocol-specific options are pulled from the server menu and <strong>must</strong> include
                                <code class="text-blue-600">-c</code> &amp; <code class="text-blue-600">-i</code>. Protocols must match:
                                TCP=<code class="text-blue-600">-S</code>, UDP=<code class="text-blue-600">-2</code>, ICMP=<code class="text-blue-600">-1</code>. <strong>--flood is not allowed.</strong>
                            </p>
                        </div>

                        <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                            <button class="btn-primary px-5 py-2.5 font-medium"
                                    :disabled="selected.status === 'failed' || !selectedId">
                                Start Benchmark
                            </button>
                            <span class="text-sm text-red-600" x-show="selected.status === 'failed'">SSH status FAILED.</span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>