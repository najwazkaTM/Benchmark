<?php

namespace App\Http\Controllers;

use App\Models\BenchmarkResult;
use App\Models\SourceServer;

class DashboardController extends Controller
{
    public function index()
    {
        $serverCount = SourceServer::count();
        $benchCount  = BenchmarkResult::count();
        $latest      = BenchmarkResult::latest()->take(5)->get();
        return view('dashboard.index', compact('serverCount','benchCount','latest'));
    }
}
