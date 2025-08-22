<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Avalanche</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --primary-blue: #3b82f6;
            --light-blue: #dbeafe;
            --gray-40: #9ca3af;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md">
        <!-- Session Status -->
        <div class="mb-4 rounded-lg bg-blue-50 p-4 text-sm text-blue-700" role="alert">
            Session status message would appear here
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-blue-600">Avalanche</h1>
                <p class="text-gray-500 mt-1">Network Benchmark Suite</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input id="email" 
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-colors" 
                           type="email" 
                           name="email" 
                           :value="old('email')" 
                           required 
                           autofocus 
                           autocomplete="username"
                           placeholder="Enter your email">
                    <div class="text-sm text-red-600 mt-2">
                        Error messages for email would appear here
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-5">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input id="password" 
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-colors"
                           type="password"
                           name="password"
                           required 
                           autocomplete="current-password"
                           placeholder="Enter your password">
                    <div class="text-sm text-red-600 mt-2">
                        Error messages for password would appear here
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center mb-6">
                    <input id="remember_me" 
                           type="checkbox" 
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                           name="remember">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-600">
                        Remember me
                    </label>
                </div>

                <div class="flex items-center justify-between">
                    <a class="text-sm text-blue-600 hover:text-blue-800 transition-colors" href="{{ route('password.request') }}">
                        Forgot your password?
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors focus:ring-2 focus:ring-blue-100 focus:ring-offset-2">
                        Log in
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>