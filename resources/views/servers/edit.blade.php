<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Avalanche - Add Source Server</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --primary-blue: #3b82f6;
            --light-blue: #f0f9ff;
            --gray-40: #9ca3af;
        }
        
        .form-container {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
        }
        
        .input-field {
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            transition: all 0.15s ease;
            font-size: 0.875rem;
        }
        
        .input-field:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
            outline: none;
        }
        
        .btn-primary {
            background-color: #3b82f6;
            color: white;
            border-radius: 0.375rem;
            padding: 0.5rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.15s ease;
        }
        
        .btn-primary:hover {
            background-color: #2563eb;
        }
        
        .info-box {
            background-color: #f8fafc;
            border-radius: 0.375rem;
            border: 1px solid #e2e8f0;
        }
        
        .section-title {
            color: #374151;
            font-weight: 500;
            font-size: 1rem;
            margin-bottom: 1rem;
        }
        
        code {
            background-color: #f1f5f9;
            padding: 0.1rem 0.25rem;
            border-radius: 0.25rem;
            font-size: 0.75em;
            color: #3b82f6;
            font-family: 'SF Mono', Monaco, 'Cascadia Code', monospace;
        }
        
        label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.375rem;
        }
    </style>
</head>
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md h-screen">
        @include('layouts.app')
    </aside>

   <!-- Konten -->
<main class="flex-1 flex justify-center p-6 bg-gray-50 overflow-auto">
    <div class="p-4 w-full max-w-3xl"> 
        
        <!-- Header -->
        <div class="mb-4">
            <h1 class="text-lg font-semibold text-gray-800">Edit Source Server</h1>
        </div>

        <!-- Form Card -->
        <div class="bg-white max-w-3xl rounded-lg shadow-md p-4">
            <form method="POST" action="{{ route('servers.update',$server) }}" class="space-y-4">
                @csrf

                <!-- Server Details -->
                <div>
                    <h2 class="text-sm font-semibold mb-2 text-gray-700">Server Details</h2>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium">Name</label>
                            <input name="name" class="w-full border rounded px-2 py-1 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium">IP Address</label>
                            <input name="ip" class="w-full border rounded px-2 py-1 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium">SSH Port</label>
                            <input name="ssh_port" type="number" value="22" min="1" max="65535"
                                class="w-full border rounded px-2 py-1 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium">SSH User</label>
                            <input name="ssh_user" class="w-full border rounded px-2 py-1 text-sm" required>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-medium">SSH Password</label>
                            <input name="ssh_password" type="password" 
                                class="w-full border rounded px-2 py-1 text-sm" required>
                        </div>
                    </div>
                </div>

                <!-- Advanced -->
                <div>
                    <h2 class="text-sm font-semibold mb-2 text-gray-700">Advanced Config</h2>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-xs font-medium">Network Interface</label>
                            <input name="iface" class="w-full border rounded px-2 py-1 text-sm" 
                                placeholder="eth0">
                        </div>
                        <div>
                            <label class="block text-xs font-medium">hping3 Path</label>
                            <input name="hping_path" class="w-full border rounded px-2 py-1 text-sm" 
                                placeholder="/usr/sbin/hping3" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium">TCP Opt</label>
                            <input name="hping_tcp_options" class="w-full border rounded px-2 py-1 text-sm" 
                                placeholder="-S -c 2000 -i u2000 -d 64">
                            <p class="text-xs text-gray-500 mt-1">Wajib <code>-S</code> bila dipakai. Tanpa <code>--flood</code>.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium">UDP Opt</label>
                            <input name="hping_udp_options" class="w-full border rounded px-2 py-1 text-sm" 
                                placeholder="-2 -c 1500 -i u1500 -d 128">
                            <p class="text-xs text-gray-500 mt-1">Wajib <code>-2</code> bila dipakai. Tanpa <code>--flood</code>.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium">ICMP Opt</label>
                            <input name="hping_icmp_options" class="w-full border rounded px-2 py-1 text-sm" 
                                placeholder="-1 -c 1000 -i u1000 -d 64">
                            <p class="text-xs text-gray-500 mt-1">Wajib <code>-1</code> bila dipakai. Tanpa <code>--flood</code>.</p>
                        </div>
                    </div>
                </div>

                <!-- Info -->
                <div class="p-2 bg-blue-50 rounded text-xs text-gray-600">
                    Options must include <code>-c</code> &amp; <code>-i</code>. Ports are set at runtime.
                </div>

                <div class="flex items-center gap-3">
       		 <button class="bg-gray-900 text-white px-4 py-2 rounded">Update</button>
        	<form action="{{ route('servers.check', $server) }}" method="POST" class="inline">
           	 @csrf
            	<button type="submit" class="px-3 py-2 rounded bg-indigo-600 text-white">Check SSH</button>
        	</form>
   	 </div>
    </div>
</main>
