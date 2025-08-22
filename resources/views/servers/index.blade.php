@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-bold">Source Servers</h1>
    <div class="space-x-2">
        <form action="{{ route('servers.checkAll') }}" method="POST" class="inline">
            @csrf
            <button class="bg-gray-500 hover:bg-gray-400 text-white px-4 py-2.5 rounded-lg font-medium transition-colors">Check All</button>
        </form>
        <a href="{{ route('servers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg font-medium transition-colors">+ Add</a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="g-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SSH</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Iface</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">hping Path</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Checked</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
        </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
        @forelse ($servers as $server)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-semibold">{{ $server->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $server->ip }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">:{{ $server->ssh_port }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $server->ssh_user }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $server->iface ?: '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $server->hping_path ?: '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                    <span class="px-2 py-1 rounded text-xs {{ $server->ssh_status_color }}">
                        {{ $server->ssh_status ? ucfirst($server->ssh_status) : 'Unknown' }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                    {{ $server->last_checked_at ? $server->last_checked_at->format('Y-m-d H:i:s') : '-' }}
                </td>
                <td class="px-3 whitespace-nowrap">
                    <form action="{{ route('servers.check', $server) }}" method="POST" class="inline">
                        @csrf
                        <button class="text-indigo-600">Check</button>
                    </form>
                    <a href="{{ route('servers.edit',$server) }}" class="text-blue-600 ml-3">Edit</a>
                    <form action="{{ route('servers.destroy',$server) }}" method="POST" class="inline" onsubmit="return confirm('Delete this server?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 ml-3">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td class="py-3 px-3" colspan="9">No servers</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection
