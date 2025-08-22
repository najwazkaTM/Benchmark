<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Avalanche - Network Benchmark Suite</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-blue: #3b82f6;
            --light-blue: #dbeafe;
            --gray-40: #9ca3af;
        }
        
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 16rem;
            flex-shrink: 0;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            z-index: 10;
        }
        
        .main-content {
            flex-grow: 1;
            overflow: auto;
        }
        
        .stat-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            margin-bottom: 0.25rem;
            border-radius: 0.5rem;
            color: #4b5563;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .nav-item.active {
            background-color: #eef6ff;
            color: #3b82f6;
            font-weight: 600;
            border-left: 4px solid #3b82f6;
        }
        
        .nav-item:hover:not(.active) {
            background-color: #f9fafb;
        }
        
        .nav-item svg {
            margin-right: 0.75rem;
            flex-shrink: 0;
        }
        
        .mobile-menu-btn {
            display: none;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -16rem;
                height: 100vh;
                transition: transform 0.3s ease;
                z-index: 50;
            }
            
            .sidebar.active {
                transform: translateX(16rem);
            }
            
            .mobile-menu-btn {
                display: block;
                position: fixed;
                top: 1rem;
                left: 1rem;
                z-index: 60;
                background: white;
                border-radius: 0.375rem;
                padding: 0.5rem;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }
            
            .overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 40;
            }
            
            .overlay.active {
                display: block;
            }
        }
    </style>
</head>
<body class="h-full font-sans text-gray-800">
<div class="dashboard-container">
    <!-- Mobile Menu Button -->
    <button class="mobile-menu-btn" id="mobileMenuButton">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>
    
    <!-- Overlay for mobile -->
    <div class="overlay" id="overlay"></div>

    {{-- Sidebar --}}
    <aside class="sidebar">
        <div class="px-6 py-5 border-b border-gray-200">
            <div class="text-xl font-bold text-blue-600 tracking-wide flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Avalanche
            </div>
            <div class="text-xs text-gray-500 mt-1">Network Benchmark Suite</div>
        </div>
        
        <nav class="flex-1 p-4">
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>
            
            <a href="{{ route('servers.index') }}" class="nav-item {{ request()->routeIs('servers.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                </svg>
                Source Servers
            </a>
            
            <a href="{{ route('benchmarks.create') }}" class="nav-item {{ request()->routeIs('benchmarks.create') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Run Benchmark
            </a>
            
            <a href="{{ route('benchmarks.index') }}" class="nav-item {{ request()->routeIs('benchmarks.index') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Results
            </a>
            <a href="{{ route('users.index') }}" class="flex items-center py-2 px-4 rounded-lg hover:bg-gray-400/30 transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                User Management
            </a>
        </nav>
        
        <div class="p-4 border-t text-sm">
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 truncate">{{ auth()->user()->name }}</span>
                        <button class="text-gray-500 hover:text-red-500 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </div>
                </form>
            @else
                <div class="space-y-2">
                    <a class="block text-blue-600" href="{{ route('login') }}">Login</a>
                    <a class="block text-blue-600" href="{{ route('register') }}">Register</a>
                </div>
            @endauth
        </div>
    </aside>

    {{-- Content --}}
    <main class="main-content">
        <div class="p-6">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-lg font-semibold text-gray-800">Network Benchmark Dashboard</h1>
                <p class="text-xs text-gray-500">Monitor and analyze network performance metrics</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 stat-card">
                    <div class="flex items-center">
                        <div class="rounded-full bg-blue-50 p-3 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 font-medium">Source Servers</div>
                            <div class="text-3xl font-bold">{{ $serverCount }}</div>                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 stat-card">
                    <div class="flex items-center">
                        <div class="rounded-full bg-blue-50 p-3 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 font-medium">Total Benchmarks</div>
                             <div class="text-3xl font-bold">{{ $benchCount }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 stat-card">
                    <div class="flex items-center">
                        <div class="rounded-full bg-blue-50 p-3 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 font-medium">Quick Actions</div>
                            <div class="mt-2">
                            <a class="inline-block bg-gray-900 text-white px-4 py-2 rounded" href="{{ route('benchmarks.create') }}">Run Benchmark</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Latest Results Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-800">Latest Results</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PPS</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bitrate</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                              @forelse ($latest as $r)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $r->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $r->sourceServer->name }} ({{ $r->sourceServer->ip }})</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $r->target_ip }} @if($r->port) :{{ $r->port }} @endif</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $r->protocol }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ number_format($r->pps, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ number_format($r->bitrate_bps/1_000_000, 2) }} Mbps</td>
                                    <td><a class="text-blue-600" href="{{ route('benchmarks.show',$r) }}">detail</a></td>
                                </tr>
                            @empty
                                <tr><td class="py-3" colspan="8">No data</td></tr>
                            @endforelse
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    // Toggle mobile sidebar
    document.getElementById('mobileMenuButton').addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('active');
        document.getElementById('overlay').classList.toggle('active');
    });
    
    // Close sidebar when clicking on overlay
    document.getElementById('overlay').addEventListener('click', function() {
        document.querySelector('.sidebar').classList.remove('active');
        this.classList.remove('active');
    });
</script>
</body>
</html>