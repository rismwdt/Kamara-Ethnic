<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Kamara Ethnic</title>
        <link rel="icon" href="{{ asset('img/title.png') }}" type="image/x-icon" class="rounded-full">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
            integrity="..." crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- CSS & JS -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script src="//unpkg.com/alpinejs" defer></script>

        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    </head>

    <body class="font-sans antialiased">
        <div class="min-h-screen bg-white dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Content -->
            <main class="pt-16">
                <div class="flex">
                    @include('layouts.sidebar')
                    <div class="flex-1 pl-0 lg:pl-64">
                        @isset($header)
                            <header class="sticky top-16 bg-white dark:bg-gray-800 shadow z-30">
                                <div class="py-6 px-6">
                                    {{ $header }}
                                </div>
                            </header>
                        @endisset
                        <div>
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </main>
        </div>
        {{-- <x-modal-delete ... /> --}}
    </body>
</html>
