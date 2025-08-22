@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-[#fff]">


    <!-- Konten -->
<main class="flex-1 p-4 bg-gray-50 overflow-auto">
    <div class="p-4 max-w-l mx-auto"> 
        
        <!-- Header -->
        <div class="mb-4">
                <h1 class="text-xl font-semibold text-[#5A5252]">Attack Parameters</h1>
                <p class="text-sm text-[#5A5252]/70 mt-1">Configure your attack settings precisely</p>
            </div>

            {{-- Form Section --}}
            <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="space-y-6 bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    {{-- Name and Email --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Name Field --}}
                        <div class="space-y-1">
                            <label for="name" class="block text-sm font-medium text-gray-600">Full Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                class="w-full px-3 py-2 border border-gray-200 rounded-md focus:ring-1 focus:ring-[#BF5A4B] focus:border-[#BF5A4B] transition text-gray-700 placeholder-gray-400"
                                placeholder="Name">
                            @error('name')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email Field --}}
                        <div class="space-y-1">
                            <label for="email" class="block text-sm font-medium text-gray-600">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                class="w-full px-3 py-2 border border-gray-200 rounded-md focus:ring-1 focus:ring-[#BF5A4B] focus:border-[#BF5A4B] transition text-gray-700 placeholder-gray-400"
                                placeholder="name@gmail.com">
                            @error('email')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Password Fields --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Password --}}
                        <div class="space-y-1">
                            <label for="password" class="block text-sm font-medium text-gray-600">Password (Optional)</label>
                            <div class="relative">
                                <input type="password" id="password" name="password"
                                    class="w-full px-3 py-2 border border-gray-200 rounded-md focus:ring-1 focus:ring-[#BF5A4B] focus:border-[#BF5A4B] transition text-gray-700 placeholder-gray-400"
                                    placeholder="Leave blank if unchanged">
                                <button type="button" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 toggle-password">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div class="space-y-1">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-600">Confirm Password</label>
                            <div class="relative">
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="w-full px-3 py-2 border border-gray-200 rounded-md focus:ring-1 focus:ring-[#BF5A4B] focus:border-[#BF5A4B] transition text-gray-700 placeholder-gray-400"
                                    placeholder="••••••••">
                                <button type="button" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 toggle-password">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex justify-end space-x-3 pt-2">
                    <a href="{{ route('users.index') }}"
                    class="px-4 py-2 border border-gray-200 rounded-md text-gray-600 hover:bg-gray-50 transition-colors text-sm">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-4 py-2 text-white rounded-md bg-[#4F46E5] hover:bg-[#3b35a9] focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-[#BF5A4B] transition-colors text-sm">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<script>
    // Password toggle functionality
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.querySelector('svg').classList.toggle('text-gray-600');
        });
    });
</script>
@endsection