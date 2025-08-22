@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Benchmark Results</h1>
    <a href="{{ route('benchmarks.create') }}" class="bg-gray-900 text-white px-4 py-2 rounded">Run Benchmark</a>
</div>

<div class="bg-white rounded shadow p-5 mb-6">
    <div class="font-semibold mb-3">Overtime (last 30 results)</div>
    <div class="grid md:grid-cols-2 gap-6">
        <div><canvas id="ppsChart" height="140"></canvas></div>
        <div><canvas id="bitChart" height="140"></canvas></div>
    </div>
</div>

<div class="bg-white rounded shadow overflow-x-auto">
<table class="min-w-full text-sm">
    <thead class="text-left text-gray-600">
    <tr>
        <th class="py-2 px-3">Time</th>
        <th class="px-3">Source</th>
        <th class="px-3">Target</th>
        <th class="px-3">Proto</th>
        <th class="px-3">Packets</th>
        <th class="px-3">PPS</th>
        <th class="px-3">Bitrate</th>
        <th class="px-3">Status</th>
        <th class="px-3"></th>
    </tr>
    </thead>
    <tbody>
    @forelse ($results as $r)
        <tr class="border-t">
            <td class="py-2 px-3">{{ $r->created_at->format('Y-m-d H:i:s') }}</td>
            <td class="px-3">{{ $r->sourceServer->name }} ({{ $r->sourceServer->ip }})</td>
            <td class="px-3">{{ $r->target_ip }} @if($r->port):{{ $r->port }}@endif</td>
            <td class="px-3">{{ $r->protocol }}</td>
            <td class="px-3">
                @if($r->packet_count && $r->packet_size)
                    {{ number_format($r->packet_count) }} × {{ $r->packet_size }}B
                @else
                    -
                @endif
            </td>
            <td class="px-3">{{ $r->pps ? number_format($r->pps, 2) : '-' }}</td>
            <td class="px-3">{{ $r->bitrate_bps ? number_format($r->bitrate_bps/1_000_000, 2) : '-' }} {{ $r->bitrate_bps ? 'Mbps' : '' }}</td>
            <td class="px-3">
                <span class="px-2 py-1 rounded text-xs
                    {{ $r->status==='finished' ? 'text-green-700 bg-green-50' : ($r->status==='running' ? 'text-yellow-700 bg-yellow-50' : 'text-gray-700 bg-gray-50') }}">
                    {{ ucfirst($r->status) }}
                </span>
            </td>
            <td class="px-3"><a class="text-blue-600" href="{{ route('benchmarks.show',$r) }}">detail</a></td>
        </tr>
    @empty
        <tr><td class="py-3 px-3" colspan="9">No results</td></tr>
    @endforelse
    </tbody>
</table>
</div>

<div class="mt-4">
    {{ $results->links() }}
</div>

<script>
const labels = @json($labels ?? []);
const ppsData = @json($ppsData ?? []);
const bitData = @json($bitData ?? []);

if (labels.length) {
    new Chart(document.getElementById('ppsChart'), {
        type: 'line',
        data: { labels, datasets: [{ label: 'PPS', data: ppsData, tension: 0.25 }] },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
    new Chart(document.getElementById('bitChart'), {
        type: 'line',
        data: { labels, datasets: [{ label: 'Bitrate (Mbps)', data: bitData, tension: 0.25 }] },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
}
</script>
@endsection
