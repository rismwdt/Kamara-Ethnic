<nav x-data="{ open: false }" class="fixed top-0 left-0 w-full z-40 bg-white dark:bg-gray-800 border-b
            border-gray-200 dark:border-gray-700 overflow-x-hidden">
    <div class="mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto rounded-full fill-current" />
                    </a>
                </div>
                <div class="ms-3">
                    <div class="font-semibold text-lg text-gray-900 dark:text-white leading-tight">
                        Kamara Ethnic
                    </div>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                </div>
            </div>

            @php
            $photoUrl = Auth::user()->profile_photo_url ?? null; // contoh Jetstream
            $initial = strtoupper(substr(Auth::user()->name ?? 'U', 0, 1));
            @endphp

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2">
                    @if ($photoUrl)
                    <img src="{{ $photoUrl }}" alt="avatar"
                        class="h-8 w-8 rounded-full object-cover ring-1 ring-gray-300">
                    @else
                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full
                   bg-gray-200 text-gray-700 text-sm font-semibold ring-1 ring-gray-300">
                        {{ $initial }}
                    </span>
                    @endif
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ Auth::user()->name }}
                    </span>
                </a>

                {{-- <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium
             bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700
             dark:text-gray-200 dark:hover:bg-gray-600">
                    {{ __('Log Out') }}
                </button>
                </form> --}}
            </div>

            @php
            use App\Models\Booking;
            $pendingBookings = isset($pendingBookings)
            ? (int) $pendingBookings
            : ((Auth::check() && Auth::user()->hasAnyRole(['admin','owner']))
            ? Booking::where('status','tertunda')->count()
            : 0);
            @endphp


            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="relative inline-flex items-center justify-center p-2 rounded-md
           text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400
           hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none
           transition duration-150 ease-in-out" aria-label="Buka menu">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>

                    @hasanyrole('admin|owner')
                    @if($pendingBookings > 0)
                    <span class="absolute top-0 right-0 -translate-y-1/3 translate-x-1/3
                 inline-flex items-center justify-center
                 h-5 min-w-[1.15rem] px-1.5
                 text-[10px] font-bold leading-none text-white
                 bg-red-600 rounded-full z-10
                 ring-2 ring-white dark:ring-gray-800">
                        {{ $pendingBookings }}
                    </span>
                    <span class="sr-only">Pesanan menunggu: {{ $pendingBookings }}</span>
                    @endif
                    @endhasanyrole
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}"
        class="hidden sm:hidden max-h-[calc(100vh-4rem)] overflow-y-auto overscroll-contain bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
        @role('admin')
        <div class="pt-2 pb-1 space-y-1">
            <div class="px-4 mt-2 mb-1 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">
                {{ __('DASHBOARD') }}
            </div>
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-tachometer-alt text-gray-500"></i>
                    {{ __('Dashboard') }}
                </span>
            </x-responsive-nav-link>
        </div>

        <div class="pt-2 pb-1 space-y-1">
            <div class="px-4 mt-2 mb-1 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">
                {{ __('DATA MASTER') }}
            </div>
            <x-responsive-nav-link :href="route('paket-acara.index')" :active="request()->routeIs('paket-acara.*')">
                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-masks-theater text-gray-500"></i>
                    {{ __('Paket Acara') }}
                </span>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('peran.index')" :active="request()->routeIs('peran.*')">
                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-user-tag text-gray-500"></i>
                    {{ __('Peran Pengisi Acara') }}
                </span>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('pengisi-acara.index')" :active="request()->routeIs('pengisi-acara.*')">
                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-users text-gray-500"></i>
                    {{ __('Pengisi Acara') }}
                </span>
            </x-responsive-nav-link>
        </div>

        <div class="pt-2 pb-1 space-y-1">
            <div class="px-4 mt-2 mb-1 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">
                {{ __('DATA OPERASIONAL') }}
            </div>
            <x-responsive-nav-link :href="route('pesanan.index')" :active="request()->routeIs('pesanan.*')">
                <span class="inline-flex items-center justify-between w-full">
                    <span class="inline-flex items-center gap-2">
                        <i class="fas fa-file-invoice text-gray-500"></i>
                        {{ __('Pesanan') }}
                    </span>
                    @if(!empty($pendingBookings) && $pendingBookings > 0)
                    <span
                        class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                        {{ $pendingBookings }}
                    </span>
                    @endif
                </span>
            </x-responsive-nav-link>
        </div>

        <div class="pt-2 pb-1 space-y-1">
            <div class="px-4 mt-2 mb-1 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">
                {{ __('PENGATURAN') }}
            </div>
            <x-responsive-nav-link :href="route('pengaturan-paket-acara.index')"
                :active="request()->routeIs('pengaturan-paket-acara.*')">
                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-cogs text-gray-500"></i>
                    {{ __('Pengaturan Paket Acara') }}
                </span>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('akun.index')" :active="request()->routeIs('akun.*')">
                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-user-cog text-gray-500"></i>
                    {{ __('Manajemen Akun') }}
                </span>
            </x-responsive-nav-link>
        </div>

        <div class="pt-3 pb-1 mt-2 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                    <span class="inline-flex items-center gap-2">
                        <i class="fas fa-user text-gray-500"></i>
                        {{ __('Profile') }}
                    </span>
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        <span class="inline-flex items-center gap-2">
                            <i class="fas fa-sign-out-alt text-gray-500"></i>
                            {{ __('Logout') }}
                        </span>
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @endrole

        @role('owner')
        <div class="pt-2 pb-1 space-y-1">
            <div class="px-4 mt-2 mb-1 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">
                {{ __('DASHBOARD') }}
            </div>
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-tachometer-alt text-gray-500"></i>
                    {{ __('Dashboard') }}
                </span>
            </x-responsive-nav-link>
        </div>

        <div class="pt-2 pb-1 space-y-1">
            <div class="px-4 mt-2 mb-1 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">
                {{ __('DATA MASTER') }}
            </div>
            <x-responsive-nav-link :href="route('paket-acara.index')" :active="request()->routeIs('paket-acara.*')">
                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-masks-theater text-gray-500"></i>
                    {{ __('Paket Acara') }}
                </span>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('pengisi-acara.index')" :active="request()->routeIs('pengisi-acara.*')">
                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-users text-gray-500"></i>
                    {{ __('Pengisi Acara') }}
                </span>
            </x-responsive-nav-link>
        </div>

        <div class="pt-2 pb-1 space-y-1">
            <div class="px-4 mt-2 mb-1 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">
                {{ __('DATA OPERASIONAL') }}
            </div>
            <x-responsive-nav-link :href="route('pesanan.index')" :active="request()->routeIs('pesanan.*')">
                <span class="relative inline-flex items-center w-full">
                    <span class="inline-flex items-center gap-2 flex-1 pr-8">
                        <i class="fas fa-file-invoice text-gray-500"></i>
                        {{ __('Pesanan') }}
                    </span>
                    @if($pendingBookings > 0)
                    <span class="absolute right-2 top-1/2 -translate-y-1/2
               inline-flex items-center justify-center
               h-5 min-w-[1.15rem] px-1.5
               text-[10px] font-bold leading-none text-white
               bg-red-600 rounded-full
               ring-2 ring-white dark:ring-gray-800">
                        {{ $pendingBookings }}
                    </span>
                    @endif
                </span>
            </x-responsive-nav-link>
        </div>

        <div class="pt-3 pb-1 mt-2 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                    <span class="inline-flex items-center gap-2">
                        <i class="fas fa-user text-gray-500"></i>
                        {{ __('Profile') }}
                    </span>
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        <span class="inline-flex items-center gap-2">
                            <i class="fas fa-sign-out-alt text-gray-500"></i>
                            {{ __('Logout') }}
                        </span>
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @endrole

        @role('performer')
        <div class="pt-2 pb-1 space-y-1">
            <div class="px-4 mt-2 mb-1 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">
                {{ __('DASHBOARD') }}
            </div>
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-tachometer-alt text-gray-500"></i>
                    {{ __('Dashboard') }}
                </span>
            </x-responsive-nav-link>
        </div>

        <div class="pt-3 pb-1 mt-2 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                    <span class="inline-flex items-center gap-2">
                        <i class="fas fa-user text-gray-500"></i>
                        {{ __('Profil') }}
                    </span>
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        <span class="inline-flex items-center gap-2">
                            <i class="fas fa-sign-out-alt text-gray-500"></i>
                            {{ __('Logout') }}
                        </span>
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @endrole
    </div>
</nav>
