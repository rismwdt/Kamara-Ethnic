<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Akun') }}
        </h2>
    </x-slot>

    <main class="flex-1 mb-auto bg-white min-h-screen p-6 text-gray-900 flex flex-col">
        <div class="flex justify-between items-center mb-4">
            <a href="{{ route('akun.index') }}">
                <x-secondary-button>
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </x-secondary-button>
            </a>
        </div>

        <form action="{{ route('akun.store') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <div>
                    <x-input-label for="performer_id" value="Nama Pengisi Acara" />
                    <select id="performer_id" name="performer_id" class="mt-1 block w-full rounded-md border-gray-300">
                        <option value="">-- Pilih Pengisi Acara --</option>
                        @foreach ($performers as $performer)
                        <option value="{{ $performer->id }}"
                            {{ old('performer_id') == $performer->id ? 'selected' : '' }}>
                            {{ $performer->name }}
                        </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('performer_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                        value="{{ old('email') }}" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" value="Password" />
                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required
                        autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                        class="mt-1 block w-full" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <x-primary-button class="mt-6">Simpan</x-primary-button>
            </div>
        </form>
    </main>
</x-app-layout>
