<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benchmark Results - Avalanche</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-blue: #3b82f6;
            --light-blue: #dbeafe;
            --gray-40: #9ca3af;
        }
    </style>
</head>
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md h-screen fixed">
     @include('layouts.app')
    </aside>

    <!-- Konten -->
<main class="fflex-1 ml-64 p-4 bg-gray-50 overflow-auto">
    <div class="p-4 max-w-l mx-auto">  
        <!-- Header Section -->
        <div class="flex items-center justify-between mb-6">
            <div>
              <h1 class="text-xl font-bold mb-4">Benchmark Detail</h1>
            </div>

    <div class="grid md:grid-cols-2 gap-6">
      <div class="bg-white p-5 rounded shadow">
        <div class="font-semibold mb-3">Meta</div>
        <dl class="text-sm grid grid-cols-2 gap-2">
          <dt class="text-gray-500">Source</dt><dd>{{ $benchmark->sourceServer->name }} ({{ $benchmark->sourceServer->ip }})</dd>
          <dt class="text-gray-500">Iface</dt><dd>{{ $benchmark->iface_used ?: $benchmark->sourceServer->iface ?: '-' }}</dd>
          <dt class="text-gray-500">Target</dt><dd>{{ $benchmark->target_ip }} @if($benchmark->port):{{ $benchmark->port }}@endif</dd>
          <dt class="text-gray-500">Protocol</dt><dd>{{ $benchmark->protocol }}</dd>
          <dt class="text-gray-500">Started</dt><dd>{{ optional($benchmark->started_at)->format('Y-m-d H:i:s') ?: '-' }}</dd>
          <dt class="text-gray-500">Stopped</dt><dd>{{ optional($benchmark->stopped_at)->format('Y-m-d H:i:s') ?: '-' }}</dd>
          <dt class="text-gray-500">Status</dt><dd>{{ ucfirst($benchmark->status) }}</dd>
        </dl>

        @if($benchmark->status === 'running')
          <form method="POST" action="{{ route('benchmarks.stop', $benchmark) }}" class="mt-4">
            @csrf
            <button class="bg-red-600 text-white px-4 py-2 rounded">Stop</button>
          </form>
        @endif
      </div>

      <div class="bg-white p-5 rounded shadow">
        <div class="font-semibold mb-3">Raw Output</div>
        <pre class="text-xs bg-gray-900 text-white p-3 rounded overflow-auto max-h-[420px]">{{ $benchmark->raw_output }}</pre>
      </div>
    </div>

    @if($benchmark->status === 'running')
    <div class="bg-white p-5 rounded shadow mt-6">
      <div class="font-semibold mb-3">Realtime</div>
      <div class="grid md:grid-cols-2 gap-6">
        <div><canvas id="ppsLive" height="140"></canvas></div>
        <div><canvas id="mbpsLive" height="140"></canvas></div>
      </div>
      <p class="text-xs text-gray-500 mt-2">Mengukur dari counter TX interface pada host sumber setiap ~1 detik.</p>
    </div>
    @endif

    <script>
    @if($benchmark->status === 'running')
    const liveLabels = [];
    const livePPS = [];
    const liveMBPS = [];
    const maxPoints = 120;

    const ppsChart = new Chart(document.getElementById('ppsLive'), {
      type: 'line',
      data: { labels: liveLabels, datasets: [{ label: 'PPS', data: livePPS, tension: .25 }] },
      options: { animation: false, responsive: true, scales: { y: { beginAtZero: true } } }
    });
    const mbpsChart = new Chart(document.getElementById('mbpsLive'), {
      type: 'line',
      data: { labels: liveLabels, datasets: [{ label: 'Mbps', data: liveMBPS, tension: .25 }] },
      options: { animation: false, responsive: true, scales: { y: { beginAtZero: true } } }
    });

    const es = new EventSource("{{ route('benchmarks.stream',$benchmark) }}");
    es.onmessage = (e) => {
      try {
        const d = JSON.parse(e.data);
        liveLabels.push(d.ts);
        livePPS.push(d.pps);
        liveMBPS.push(d.mbps);
        if (liveLabels.length > maxPoints) {
          liveLabels.shift(); livePPS.shift(); liveMBPS.shift();
        }
        ppsChart.update(); mbpsChart.update();
      } catch (_e) {}
    };
    es.addEventListener('done', () => es.close());
    @endif
    </script>
  </main>
</html>