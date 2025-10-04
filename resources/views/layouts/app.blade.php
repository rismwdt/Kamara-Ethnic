<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Kamara Ethnic</title>
    <link rel="icon" href="{{ asset('img/title.png') }}" type="image/x-icon" class="rounded-full">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//unpkg.com/alpinejs" defer></script>

    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
</head>

<body x-data="{ mobileNav:false }" x-effect="document.documentElement.classList.toggle('overflow-hidden', mobileNav)"
    class="font-sans antialiased overflow-x-hidden [touch-action:pan-y]">
    <div class="min-h-screen bg-white dark:bg-gray-900 overflow-x-hidden">

        @include('layouts.navigation')

        <main class="pt-16">
            <div class="flex overflow-x-hidden">

                @include('layouts.sidebar')

                <div class="flex-1 min-w-0 pl-0 lg:pl-64">
                    @isset($header)
                    <header class="fixed top-16 left-0 lg:left-64 right-0
                   bg-white dark:bg-gray-800 border-b z-30">
                        <div class="h-16 flex items-center px-4 sm:px-6">
                            <h2 class="font-semibold text-base sm:text-lg lg:text-xl text-gray-800 dark:text-gray-200">
                                {{ $header }}
                            </h2>
                        </div>
                    </header>

                    <div class="h-16"></div>
                    @endisset

                    <div class="min-w-0">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
