<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kamara Ethnic</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="..." crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- <script src="//unpkg.com/alpinejs" defer></script> --}}

    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
</head>

{{-- <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col"> --}}

<body class="bg-white text-[#1b1b18] min-h-screen">
    <nav x-data="{ open: false }"
        class="fixed top-0 left-0 w-full z-40 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex w-full justify-between items-center">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center ">
                        <a href="{{ route('welcome') }}">
                            <x-application-logo
                                class="block h-12 w-auto rounded-full fill-current text-gray-800 dark:text-gray-200" />
                        </a>
                    </div>
                    @include('klien.navbar')
                    <!-- Hamburger -->
                    <div class="-me-2 flex items-center sm:hidden">
                        <button @click="open = ! open"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Responsive Navigation Menu -->
            @include('klien.dropdown')
    </nav>

    <!-- Home section start -->
    @include('klien.home')
    <!-- Home section end -->

    <!-- Tentang kami section start -->
    @include('klien.tentang-kami')
    <!-- Tentang kami section end -->

    <!-- Paket section start -->
    @include('klien.paket-acara', ['events' => $events])

    <!-- Paket section end -->

    <!-- Galeri section start -->
    {{-- @include('klien.galeri') --}}
    <!-- Galeri section end -->

    <!-- Kontak section start -->
    @include('klien.kontak-kami')
    <!-- Kontak section end -->
</body>

<!-- Footer start -->
@include('klien.footer')
<!-- Footer end -->

<!-- Back to top Start -->
<a href="#home"
    class="fixed bottom-4 right-4 z-[9999] hidden h-14 w-14 items-center justify-center rounded-full bg-primary p-4 hover:animate-pulse"
    id="to-top">
    <span class="mt-2 block h-5 w-5 rotate-45 border-t-2 border-l-2"></span>
</a>
<!-- Back to top End -->
{{-- <script src="src/js/script.js"></script> --}}
</html>
