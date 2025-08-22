<?php

namespace App\Http\Controllers;

use App\Models\BenchmarkResult;

class BenchmarkResultController extends Controller
{
    public function index()
    {
        $results = BenchmarkResult::with('sourceServer')->latest()->paginate(10);

        // Data chart global (30 entri terakhir seluruh hasil)
        $last   = BenchmarkResult::orderBy('created_at')->take(30)->get(['created_at','pps','bitrate_bps']);
        $labels = $last->map(fn($r) => $r->created_at->format('H:i:s d/m'))->toArray();
        $ppsData = $last->pluck('pps')->map(fn($v) => round((float)$v, 2))->toArray();
        $bitData = $last->pluck('bitrate_bps')->map(fn($v) => round(((int)$v)/1_000_000, 2))->toArray(); // Mbps

        return view('benchmarks.index', compact('results','labels','ppsData','bitData'));
    }

    public function show(BenchmarkResult $benchmark)
    {
        $benchmark->load('sourceServer');

        // Seri yang sama (berdasarkan source+target+proto(+port)), max 30
        $series = BenchmarkResult::where('source_server_id', $benchmark->source_server_id)
            ->where('target_ip', $benchmark->target_ip)
            ->where('protocol', $benchmark->protocol)
            ->when($benchmark->port, fn($q) => $q->where('port', $benchmark->port))
            ->orderBy('created_at')
            ->take(30)
            ->get(['created_at','pps','bitrate_bps']);

        $labels = $series->map(fn($r) => $r->created_at->format('H:i:s d/m'))->toArray();
        $ppsData = $series->pluck('pps')->map(fn($v) => round((float)$v, 2))->toArray();
        $bitData = $series->pluck('bitrate_bps')->map(fn($v) => round(((int)$v)/1_000_000, 2))->toArray(); // Mbps

        return view('benchmarks.show', compact('benchmark','labels','ppsData','bitData'));
    }
}
