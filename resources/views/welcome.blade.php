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

    <script>
    window.ENDPOINTS = {
        checkSchedule: "{{ route('cek-jadwal') }}",
        csrf: "{{ csrf_token() }}"
    };
    </script>

    @vite(['resources/css/app.css', 'resources/js/client.js'])

    <script src="//unpkg.com/alpinejs" defer></script>

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
                                class="block h-12 w-12 rounded-full fill-current text-gray-800 dark:text-gray-200" />
                        </a>
                    </div>
                    <div>
                        <nav id="nav-menu" class="hidden absolute py-5 bg-white shadow-lg rounded-lg max-w-[250px] w-full right-4 top-full lg:block lg:static lg:bg-transparent
        lg:max-w-full lg:shadow-none lg:rounded-none transition-all duration-300 ease-in-out">
                            <ul class="block lg:flex">
                                <li class="group">
                                    <a href="#home"
                                        class="text-base text-dark py-2 mx-8 flex group-hover:text-primary scroll-smooth transition-colors duration-200">
                                        Home
                                    </a>
                                </li>
                                <li class="group">
                                    <a href="#about"
                                        class="text-base text-dark py-2 mx-8 flex group-hover:text-primary scroll-smooth transition-colors duration-200">
                                        Tentang Kami
                                    </a>
                                </li>
                                <li class="group">
                                    <a href="#event"
                                        class="text-base text-dark py-2 mx-8 flex group-hover:text-primary scroll-smooth transition-colors duration-200">
                                        Paket Acara
                                    </a>
                                </li>
                                <li class="group">
                                    <a href="#contact"
                                        class="text-base text-dark py-2 mx-8 flex group-hover:text-primary scroll-smooth transition-colors duration-200">
                                        Kontak Kami
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <nav class="flex items-center justify-end gap-4">
                            @guest
                            <a href="{{ route('login') }}"
                                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                                Log in
                            </a>
                            <a href="{{ route('register') }}"
                                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                Register
                            </a>
                            @endguest
                        </nav>
                    </div>
                </div>
                <!-- Settings Dropdown -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    @auth
                    @if (Auth::user()->hasRole('client'))
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            {{-- <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link> --}}
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                    @endif
                    @endauth
                </div>
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
        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link href="#home">
                    Home
                </x-responsive-nav-link>
                <x-responsive-nav-link href="#about">
                    Tentang Kami
                </x-responsive-nav-link>
                <x-responsive-nav-link href="#event">
                    Paket Acara
                </x-responsive-nav-link>
                <x-responsive-nav-link href="#contact">
                    Kontak Kami
                </x-responsive-nav-link>
            </div>
            @guest
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('login')">
                    {{ __('Login') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')">
                    {{ __('Register') }}
                </x-responsive-nav-link>
            </div>
            @endguest
            @auth
            @if (Auth::user()->hasRole('client'))
            <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
                <div class="mt-3 space-y-1">
                    {{-- <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link> --}}
                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
            @endif
            @endauth
        </div>
    </nav>

    <!-- Home section start -->
    @include('klien.home')
    <!-- Home section end -->

    <!-- Tentang kami section start -->
    @include('klien.tentang-kami')
    <!-- Tentang kami section end -->

    <!-- Paket section start -->
    @include('klien.paket-acara', ['events' => $events])
    {{-- @include('klien.modal-pesanan') --}}
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
