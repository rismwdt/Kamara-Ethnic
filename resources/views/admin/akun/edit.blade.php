<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Akun') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="mx-auto px-5 space-y-6 max-w-3xl">

            @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded">
                {{ session('success') }}
            </div>
            @endif

            <div class="flex justify-between items-center mb-4">
                <a href="{{ route('akun.index') }}">
                    <x-secondary-button>
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </x-secondary-button>
                </a>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-md sm:rounded-lg">
                <header>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Informasi Profil</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Perbarui nama dan alamat email akun ini.
                    </p>
                </header>

                <form method="post" action="{{ route('akun.update', $user) }}" class="mt-6 space-y-6">
                    @csrf
                    @method('patch')

                    <div>
                        <x-input-label for="name" :value="__('Nama')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            :value="old('name', $user->name)" required autofocus autocomplete="name" />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                            :value="old('email', $user->email)" required autocomplete="username" />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>Simpan</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-md sm:rounded-lg">
                <header>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Perbarui Password</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Setel password baru untuk akun ini.
                    </p>
                </header>

                <form method="post" action="{{ route('akun.password.update', $user) }}" class="mt-6 space-y-6">
                    @csrf
                    @method('put')

                    <div>
                        <x-input-label for="password" :value="__('Password Baru')" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full"
                            required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                            class="mt-1 block w-full" required />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>Simpan</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
