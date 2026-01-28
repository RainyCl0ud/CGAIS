<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>USTP Balubal Campus - Guidance and Counsiling Services</title>

    <!-- Favicon -->
    <link rel="icon" type="image/jfif" href="/storage/logo.jfif">
    <link rel="shortcut icon" type="image/jfif" href="/storage/logo.jfif">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen min-w-full bg-gradient-to-br from-yellow-100 via-white to-blue-100 overflow-hidden">
    <div class="flex flex-col items-center justify-center min-h-screen w-full">
        <div class="w-full max-w-2xl mx-auto p-16 rounded-2xl shadow-2xl bg-white/90 border border-blue-100 animate-fade-in">
            <div class="flex flex-col items-center">
                <div class="text-center">
                    <span class="block text-5xl font-serif text-blue-900 mb-2 tracking-tight">USTP Balubal</span>
                    <span class="block text-5xl sm:text-6xl md:text-7xl font-extrabold font-black bg-gradient-to-r from-[#FFD700] to-blue-700 bg-clip-text text-transparent leading-tight">CGAIS</span>
                </div>
                <div class="w-32 h-2 bg-[#FFD700] rounded mb-10 animate-slide-in"></div>
                <h1 class="text-6xl font-extrabold text-blue-800 mb-4 text-center bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent drop-shadow-lg animate-pulse">Welcome!</h1>
                <div class="flex gap-8 mb-2">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-10 py-4 text-lg bg-[#FFD700] hover:bg-[#FFE44D] hover:scale-105 text-[#1E3A8A] font-bold rounded-lg shadow transition-all duration-300 border border-blue-200">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="px-10 py-4 text-lg bg-blue-600 hover:bg-blue-700 hover:scale-105 text-white font-bold rounded-lg shadow transition-all duration-300 border border-blue-700">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-10 py-4 text-lg bg-[#FFD700] hover:bg-[#FFE44D] hover:scale-105 text-[#1E3A8A] font-bold rounded-lg shadow transition-all duration-300 border border-blue-200">Register</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
        <footer class="mt-8 text-sm text-gray-400 text-center w-full">
            &copy; {{ date('Y') }} USTP Balubal Campus - Guidance and Counsiling Services. All rights reserved.
        </footer>
    </div>
</body>
</html>
