<nav x-data="{ open: false }"
    class="fixed top-0 left-0 w-full z-40 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex w-full justify-between items-center">
                <div class="shrink-0 flex items-center">
                    <a href="{{ url('/') }}" class="flex items-center gap-2">
                        <x-application-logo
                            class="block h-12 w-12 rounded-full fill-current text-gray-800 dark:text-gray-200" />
                        <span class="block md:hidden font-bold text-gray-800 dark:text-gray-200">
                            Kamara Ethnic
                        </span>
                    </a>
                </div>

                <div>
                    <nav id="nav-menu" class="hidden absolute py-5 bg-white shadow-lg rounded-lg max-w-[250px] w-full right-4 top-full
                      lg:block lg:static lg:bg-transparent lg:max-w-full lg:shadow-none lg:rounded-none
                      transition-all duration-300 ease-in-out">
                        <ul class="block lg:flex">
                            <li class="group">
                                <a href="{{ url('/#home') }}"
                                    class="text-base text-dark py-2 mx-8 flex group-hover:text-primary">Home</a>
                            </li>
                            <li class="group">
                                <a href="{{ url('/#about') }}"
                                    class="text-base text-dark py-2 mx-8 flex group-hover:text-primary">Tentang Kami</a>
                            </li>
                            <li class="group">
                                <a href="{{ url('/#event') }}"
                                    class="text-base text-dark py-2 mx-8 flex group-hover:text-primary">Paket Acara</a>
                            </li>
                            @auth
                            <li class="group">
                                <a href="{{ route('booking.my') }}"
                                    class="text-base text-dark py-2 mx-8 flex group-hover:text-primary">
                                    Pesanan
                                </a>
                            </li>
                            @endauth
                        </ul>
                    </nav>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <nav class="flex items-center justify-end gap-4">
                        @guest
                        <a href="{{ route('login') }}"
                            class="inline-block px-5 py-1.5 text-[#1b1b18] border border-transparent hover:border-[#19140035] rounded-sm text-sm">
                            Log in
                        </a>
                        <a href="{{ route('register') }}"
                            class="inline-block px-5 py-1.5 border text-[#1b1b18] border-[#19140035] hover:border-[#1915014a] rounded-sm text-sm">
                            Register
                        </a>
                        @endguest
                    </nav>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                @if (Auth::user()->hasRole('client'))
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md
                         text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300">
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

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500
                       hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ url('/#home') }}">Home</x-responsive-nav-link>
            <x-responsive-nav-link href="{{ url('/#about') }}">Tentang Kami</x-responsive-nav-link>
            <x-responsive-nav-link href="{{ url('/#event') }}">Paket Acara</x-responsive-nav-link>
            @auth
            <x-responsive-nav-link :href="route('booking.my')">
                Pesanan
            </x-responsive-nav-link>
            @endauth

        </div>

        @guest
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('login')">{{ __('Login') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('register')">{{ __('Register') }}</x-responsive-nav-link>
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
