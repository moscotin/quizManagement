<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex items-center justify-center min-h-screen flex-col relative">
        <div class="absolute w-full px-12 top-0">
            <div class="flex w-full mt-4">
                <!-- Logo floated left -->
                <div class="w-full block text-center lg:text-left">
                    <a href="/">
                        <img src="/img/owl.webp" alt="Logo" class="h-24 mx-auto lg:mx-0" />
                    </a>
                </div>
            </div>
        </div>

        <div class="w-full sm:max-w-lg mt-24 mb-32 px-6 py-8 bg-white dark:bg-[#1a1a1a] shadow-lg overflow-hidden rounded-lg">
            {{ $slot }}
        </div>

        <div class="absolute bottom-0 w-full px-12 mb-4">
            <div class="flex w-full">
                <div class="w-full block text-center">
                    <img src="/img/mow.webp" alt="MOSKWA" class="h-24 mx-auto" />
                </div>
            </div>
        </div>
    </body>
</html>
