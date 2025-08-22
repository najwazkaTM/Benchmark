@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-4">

    {{-- Main content --}}
    <main class="flex-1 p-4 bg-gray-50 overflow-auto">
         <div class="flex items-center justify-between mb-4">  
                <div>
                    <h1 class="text-2xl font-semibold text-[#5A5252]">User Management</h1>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <a href="{{ route('users.create') }}" class="flex items-center px-4 py-2 bg-[#4F46E5] hover:bg-[#3b35a9] rounded-md text-white text-sm font-medium transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 4v16m8-8H4"/>
                        </svg>
                        Add User
                    </a>
                </div>
            </div>


    {{-- user Table --}}
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-6 border-b border-[#C9C9C9]">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-[#5A5252]">User</h2>
                    <form action="{{ route('users.index') }}" method="GET">
                        <div class="relative">
                            <input type="text" name="search" id="user-search-input" value="{{ request('search') }}" placeholder="Search by name or email..." 
                                class="border border-[#C9C9C9] rounded-md text-xs pl-5 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#4F46E5]">
                            <i class="fas fa-search absolute left-3 top-3 text-[#5A5252]"></i>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#C9C9C9]">
                    <thead class="bg-[#000]/50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#C9C9C9]">
                        <!-- Critical Attack -->
                        @foreach($users as $user)
                        <tr class="hover:bg-[#f5f5f5]">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[#5A5252]">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#5A5252]">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center space-x-4">
                                    {{-- Tombol Edit User --}}
                                    <a href="{{ route('users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                        Edit
                                    </a>

                                    {{-- Tombol Delete User --}}
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-medium">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<script>
    document.addEventListener('DOMContentLoaded', function() {
    });

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('user-search-input');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                if (this.value === '') {
                    window.location.href = '{{ route("users.index") }}';
                }
            });
        }
    });
</script>
@endsection
