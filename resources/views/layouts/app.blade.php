<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/jfif" href="/storage/logo.jfif">
        <link rel="shortcut icon" type="image/jfif" href="/storage/logo.jfif">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-blue-yellow-gradient bg-fixed bg-cover bg-center">
            <!-- 1. Header - Always on top -->
            @include('layouts.navigation')
            
                       <!-- 2. Sidebar - Covers part of the header -->
           <div class="fixed top-0 left-0 h-full z-50 pointer-events-none">
                @include('components.dashboard-sidebar')
            </div>
            
            <!-- 3. Main Content Area - Below header, beside sidebar -->
            <div class="pt-16 lg:pl-64"> <!-- pt-16 for header height, lg:pl-64 for sidebar width -->
                <!-- Page Content -->
                <main class="min-h-screen">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- Auto-hide success and error messages -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Function to add close button to messages
                function addCloseButton(message) {
                    const closeButton = document.createElement('button');
                    closeButton.innerHTML = '×';
                    closeButton.className = 'float-right font-bold text-lg text-gray-500 hover:text-gray-700 ml-2';
                    closeButton.style.cssText = 'background: none; border: none; cursor: pointer; padding: 0; line-height: 1;';
                    closeButton.onclick = function() {
                        message.style.transition = 'opacity 0.3s ease-out';
                        message.style.opacity = '0';
                        setTimeout(function() {
                            message.remove();
                        }, 300);
                    };
                    
                    // Insert close button at the beginning of the message
                    message.insertBefore(closeButton, message.firstChild);
                }

                // Auto-hide session success messages after 5 seconds
                // Only target divs with mb-4 and specific session message classes
                const successMessages = document.querySelectorAll('div.mb-4.bg-green-100.border-green-400.text-green-700');
                successMessages.forEach(function(message) {
                    addCloseButton(message);
                    setTimeout(function() {
                        if (message.parentNode) { // Check if message still exists
                            message.style.transition = 'opacity 0.5s ease-out';
                            message.style.opacity = '0';
                            setTimeout(function() {
                                if (message.parentNode) {
                                    message.remove();
                                }
                            }, 500);
                        }
                    }, 5000);
                });

                // Auto-hide session error messages after 8 seconds (longer for errors)
                // Only target divs with mb-4 and specific session message classes
                const errorMessages = document.querySelectorAll('div.mb-4.bg-red-100.border-red-400.text-red-700');
                errorMessages.forEach(function(message) {
                    addCloseButton(message);
                    setTimeout(function() {
                        if (message.parentNode) { // Check if message still exists
                            message.style.transition = 'opacity 0.5s ease-out';
                            message.style.opacity = '0';
                            setTimeout(function() {
                                if (message.parentNode) {
                                    message.remove();
                                }
                            }, 500);
                        }
                    }, 8000);
                });
            });
        </script>
    </body>
</html>
