<aside class="hidden lg:block fixed top-16 left-0 h-[calc(100vh-4rem)]
            w-48 lg:w-64 bg-white border-r border-gray-200 z-30
            overflow-y-auto overflow-x-hidden [overscroll-behavior:contain]">
    <div>
        <nav class="px-4 py-4 text-sm font-medium space-y-4">

            @hasanyrole('admin|owner')
            <section class="space-y-1">
                <div class="px-2 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">
                    {{ __('DASHBOARD') }}
                </div>
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                    class="flex items-center gap-3 py-2 px-3 w-full   hover:bg-gray-100">
                    <i class="fas fa-tachometer-alt text-gray-500 shrink-0"></i>
                    <span>{{ __('Dashboard') }}</span>
                </x-nav-link>
            </section>

            <section class="space-y-1">
                <div class="px-2 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">
                    {{ __('DATA MASTER') }}
                </div>

                <x-nav-link :href="route('paket-acara.index')" :active="request()->routeIs('paket-acara.*')"
                    class="flex items-center gap-3 py-2 px-3 w-full   hover:bg-gray-100">
                    <i class="fas fa-masks-theater text-gray-500 shrink-0"></i>
                    <span>{{ __('Paket Acara') }}</span>
                </x-nav-link>

                @role('admin')
                <x-nav-link :href="route('peran.index')" :active="request()->routeIs('peran.*')"
                    class="flex items-center gap-3 py-2 px-3 w-full   hover:bg-gray-100">
                    <i class="fas fa-user-tag text-gray-500 shrink-0"></i>
                    <span>{{ __('Peran Pengisi Acara') }}</span>
                </x-nav-link>
                @endrole

                <x-nav-link :href="route('pengisi-acara.index')" :active="request()->routeIs('pengisi-acara.*')"
                    class="flex items-center gap-3 py-2 px-3 w-full   hover:bg-gray-100">
                    <i class="fas fa-users text-gray-500 shrink-0"></i>
                    <span>{{ __('Pengisi Acara') }}</span>
                </x-nav-link>
            </section>

            <section class="space-y-1">
                <div class="px-2 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">
                    {{ __('DATA OPERASIONAL') }}
                </div>

                <x-nav-link :href="route('pesanan.index')" :active="request()->routeIs('pesanan.*')"
                    class="flex items-center justify-between py-2 px-3 w-full   hover:bg-gray-100">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-file-invoice text-gray-500 shrink-0"></i>
                        <span>{{ __('Pesanan') }}</span>
                    </div>
                    @if(!empty($pendingBookings) && $pendingBookings > 0)
                    <span
                        class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                        {{ $pendingBookings }}
                    </span>
                    @endif
                </x-nav-link>
            </section>

            @role('admin')
            <section class="space-y-1">
                <div class="px-2 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">
                    {{ __('PENGATURAN') }}
                </div>

                <x-nav-link :href="route('pengaturan-paket-acara.index')"
                    :active="request()->routeIs('pengaturan-paket-acara.*')"
                    class="flex items-center gap-3 py-2 px-3 w-full   hover:bg-gray-100">
                    <i class="fas fa-cogs text-gray-500 shrink-0"></i>
                    <span>{{ __('Pengaturan Paket Acara') }}</span>
                </x-nav-link>

                <x-nav-link :href="route('akun.index')" :active="request()->routeIs('akun.*')"
                    class="flex items-center gap-3 py-2 px-3 w-full   hover:bg-gray-100">
                    <i class="fas fa-user-cog text-gray-500 shrink-0"></i>
                    <span>{{ __('Manajemen Akun') }}</span>
                </x-nav-link>
            </section>
            @endrole
            @endhasanyrole

            @role('performer')
            <section class="space-y-1">
                <div class="px-2 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">
                    {{ __('DASHBOARD') }}
                </div>
                <x-nav-link :href="route('performer.dashboard')" :active="request()->routeIs('performer.dashboard')"
                    class="flex items-center gap-3 py-2 px-3 w-full   hover:bg-gray-100">
                    <i class="fas fa-tachometer-alt text-gray-500 shrink-0"></i>
                    <span>{{ __('Dashboard') }}</span>
                </x-nav-link>
            </section>
            @endrole

            <section class="space-y-1 border-t pt-4">
                <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')"
                    class="flex items-center gap-3 py-2 px-3 w-full hover:bg-gray-100">
                    <i class="fas fa-user text-gray-500 shrink-0"></i>
                    <span>{{ __('Profil') }}</span>
                </x-nav-link>

                <x-nav-link :href="route('logout')"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="flex items-center gap-3 py-2 px-3 w-full hover:bg-gray-100">
                    <i class="fas fa-sign-out-alt text-gray-500 shrink-0"></i>
                    <span>{{ __('Logout') }}</span>
                </x-nav-link>
            </section>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </nav>
    </div>
</aside>
