<aside class="hidden lg:block fixed top-16 left-0 h-screen w-48 lg:w-64 bg-white border-r border-gray-200 z-30">
    <div>
        <!-- Menu Navigasi -->
        <nav class="mb-5 mt-6 pl-8 pr-8 text-sm font-medium">
            <div class="mb-2 mt-4 pl-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                {{ __('DASHBOARD') }}
            </div>
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                class="flex items-center space-x-3 py-2 px-4 pt-4 w-full hover:bg-gray-100">
                <i class="fas fa-home text-gray-500"></i>
                <span>{{ __('Dashboard') }}</span>
            </x-nav-link>
            <div class="mb-2 mt-4 pl-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                {{ __('Data Master') }}
            </div>
            <x-nav-link :href="route('paket-acara.index')" :active="request()->routeIs('paket-acara.*')"
                class="flex items-center space-x-3 py-2 px-4 pt-4 w-full hover:bg-gray-100">
                <i class="fas fa-masks-theater text-gray-500"></i>
                <span>{{ __('Paket Acara') }}</span>
            </x-nav-link>
            <x-nav-link :href="route('pengisi-acara.index')" :active="request()->routeIs('pengisi-acara.*')"
                class="flex items-center space-x-3 py-2 px-4 pt-4 w-full hover:bg-gray-100">
                <i class="fas fa-users text-gray-500"></i>
                <span>{{ __('Pengisi Acara') }}</span>
            </x-nav-link>
            <x-nav-link :href="route('lokasi-acara.index')" :active="request()->routeIs('lokasi-acara.*')"
                class="flex items-center space-x-3 py-2 px-4 pt-4 w-full hover:bg-gray-100">
                <i class="fas fa-map-marker-alt text-gray-500"></i>
                <span>{{ __('Lokasi Acara') }}</span>
            </x-nav-link>
            {{-- <x-nav-link :href="route('login')" :active="request()->routeIs('login')"
                class="flex items-center space-x-3 py-2 px-4 pt-4 w-full hover:bg-gray-100">
                <i class="fas fa-user-friends text-gray-500"></i>
                <span>{{ __('Klien') }}</span>
            </x-nav-link> --}}
            <div class="mb-2 mt-4 pl-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                {{ __('DATA OPERASIONAL') }}
            </div>
            <x-nav-link :href="route('pesanan.index')" :active="request()->routeIs('pesanan.*')"
                class="flex items-center justify-between py-2 px-4 pt-4 w-full hover:bg-gray-100">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-file-alt text-gray-500"></i>
                    <span>{{ __('Pesanan') }}</span>
                </div>
                @if(!empty($pendingBookings) && $pendingBookings > 0)
                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                        {{ $pendingBookings }}
                    </span>
                @endif
            </x-nav-link>
            {{-- <x-nav-link :href="route('login')" :active="request()->routeIs('login')"
                class="flex items-center space-x-3 py-2 px-4 pt-4 w-full hover:bg-gray-100">
                <i class="fas fa-user-friends text-gray-500"></i>
                <span>{{ __('Jadwal Acara') }}</span>
            </x-nav-link> --}}

            {{-- <div class="mb-2 mt-4 pl-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                {{ __('PENGATURAN') }}
            </div> --}}
            {{-- <x-nav-link :href="route('login')" :active="request()->routeIs('login')"
                class="flex items-center space-x-3 py-2 px-4 pt-4 w-full hover:bg-gray-100">
                <i class="fas fa-user-friends text-gray-500"></i>
                <span>{{ __('Pengarturan Sistem') }}</span>
            </x-nav-link> --}}
            {{-- <x-nav-link :href="route('login')" :active="request()->routeIs('login')"
                class="flex items-center space-x-3 py-2 px-4 pt-4 w-full hover:bg-gray-100">
                <i class="fas fa-user-friends text-gray-500"></i>
                <span>{{ __('Manajemen Admin') }}</span>
            </x-nav-link> --}}
        </nav>
        <!-- Logout -->
        {{-- <div class="pt-6 border-t space-y-3 mb-12 mt-6 pl-8 text-sm font-medium">
            <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')"
                class="flex items-center space-x-3 py-2 px-4 pt-4 w-full hover:bg-gray-100">
                <i class="fas fa-user text-gray-500"></i>
                <span>{{ __('Profile') }}</span>
            </x-nav-link>
            <x-nav-link :href="route('logout')" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                class="flex items-center space-x-3 py-2 px-4 pt-4 w-full hover:bg-gray-100">
                <i class="fas fa-sign-out-alt text-gray-500"></i>
                <span>{{ __('Logout') }}</span>
            </x-nav-link>
        </div> --}}
    </div>
</aside>


