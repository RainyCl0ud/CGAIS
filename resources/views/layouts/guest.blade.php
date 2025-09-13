<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>USTP Balubal Campus - Guidance and Counsiling Services</title>

        <!-- Favicon -->
        <link rel="icon" type="image/jfif" href="/storage/logo.jfif">
        <link rel="shortcut icon" type="image/jfif" href="/storage/logo.jfif">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased min-h-screen min-w-full bg-gradient-to-br from-yellow-100 via-white to-blue-100">
        <div class="flex flex-col items-center justify-center min-h-screen w-full overflow-y-auto py-2">
            <div class="max-w-screen-xl mx-auto mx-4 lg:mx-16 xl:mx-32 flex flex-col md:flex-row box-border">
                <!-- Left: Logo and System Description -->
                <div class="md:w-[40%] flex flex-col items-center justify-center bg-gradient-to-br from-yellow-100 via-white to-blue-100 p-6 md:p-10">
                    <div class="text-center mb-6 sm:mb-8">
                        <span class="block text-3xl sm:text-4xl md:text-5xl font-serif text-blue-900 mb-1 sm:mb-2 tracking-tight">USTP Balubal</span>
                        <span class="block text-5xl sm:text-6xl md:text-7xl font-extrabold font-black bg-gradient-to-r from-[#FFD700] to-blue-700 bg-clip-text text-transparent leading-tight">CGS</span>
                    </div>
                    <div class="text-center text-gray-700 text-base md:text-lg">
                        <p>Campus Guidance System (CGS) helps students and staff access guidance services, resources, and support efficiently. Register now to get started!</p>
                    </div>
                </div>
                <!-- Right: Registration Form -->
                <div class="md:w-[60%] w-full p-2 sm:p-4 md:p-6 flex items-center justify-center">
                    <div class="w-full">
                        {{ $slot }}
                    </div>
                </div>
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
