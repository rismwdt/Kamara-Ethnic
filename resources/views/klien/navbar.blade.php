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
                    Kontak
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
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
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
