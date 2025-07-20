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
            Kontak
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
