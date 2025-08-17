<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Pengisi Acara') }}
        </h2>
    </x-slot>

    <main class="flex-1 mb-auto bg-white min-h-screen p-6 text-gray-900 flex flex-col">
        <div class="flex justify-between items-center mb-4">
            <a href="{{ route('pengisi-acara.index') }}">
                <x-secondary-button>
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </x-secondary-button>
            </a>
        </div>

        <form method="POST" action="{{ route('pengisi-acara.store') }}">
            @csrf

            <div class="flex gap-x-6">
                {{-- Kolom kiri --}}
                <div class="w-1/2 space-y-4">
                    <div>
                        <x-input-label for="name" value="Nama" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                      value="{{ old('name') }}" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="gender" value="Jenis Kelamin" />
                        <select id="gender" name="gender" class="mt-1 block w-full rounded border-gray-300 shadow-sm" required>
                            <option value="laki-laki" {{ old('gender') === 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="perempuan" {{ old('gender') === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                            <option value="lainnya" {{ old('gender') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="performer_role_id" value="Peran" />
                        <select id="performer_role_id" name="performer_role_id" class="mt-1 block w-full rounded border-gray-300 shadow-sm" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('performer_role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('performer_role_id')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-input-label for="notes" value="Catatan" />
                        <textarea id="notes" name="notes" rows="3"
                                  class="mt-1 block w-full rounded border-gray-300 shadow-sm">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>
                </div>

                {{-- Kolom kanan --}}
                <div class="w-1/2 space-y-4">
                    <div>
                        <x-input-label for="phone" value="No. HP" />
                        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
                                      value="{{ old('phone') }}" required />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="account_number" value="Nomor Rekening" />
                        <x-text-input id="account_number" name="account_number" type="text" class="mt-1 block w-full"
                                      value="{{ old('account_number') }}" />
                        <x-input-error :messages="$errors->get('account_number')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="bank_name" value="Nama Bank" />
                        <x-text-input id="bank_name" name="bank_name" type="text" class="mt-1 block w-full"
                                      value="{{ old('bank_name') }}" />
                        <x-input-error :messages="$errors->get('bank_name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="is_active" value="Ketersediaan" />
                        <select id="is_active" name="is_active" class="mt-1 block w-full rounded border-gray-300 shadow-sm" required>
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Ya</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Tidak</option>
                        </select>
                        <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                    </div>

                    <div>
                        <input type="hidden" name="is_external" value="0">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="is_external" value="1"
                                   {{ old('is_external', 0) ? 'checked' : '' }}>
                            Talent eksternal
                        </label>
                        <x-input-error :messages="$errors->get('is_external')" class="mt-2" />
                    </div>
                </div>
            </div>

            <x-primary-button class="mt-6">Simpan</x-primary-button>
        </form>
    </main>
</x-app-layout>
