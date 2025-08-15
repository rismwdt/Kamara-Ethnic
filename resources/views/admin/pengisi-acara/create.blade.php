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
                <div class="w-1/2 space-y-4">
                    <div>
                        <x-input-label for="name" value="Nama" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required value="{{ old('name') }}"/>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="gender" value="Jenis Kelamin" />
                        <select id="gender" name="gender" class="mt-1 block w-full rounded border-gray-300 shadow-sm"
                            required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="laki-laki" {{ old('type') == 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="perempuan" {{ old('type') == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                            <option value="lainnya" {{ old('type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="performer_role_id" value="Peran" />
                        <select id="performer_role_id" name="performer_role_id" class="form-control mt-1 block w-full rounded border-gray-300 shadow-sm" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('performer_role_id')" class="mt-2" />
                    </div>
                </div>
                <div class="w-1/2 space-y-4">
                    <div>
                        <x-input-label for="phone" value="No. Hp" />
                        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" required value="{{ old('phone') }}"/>
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="account_number" value="Nomor Rekening" />
                        <x-text-input id="account_number" name="account_number" type="text" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('account_number')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="bank_name" value="Nama Bank" />
                        <x-text-input id="bank_name" name="bank_name" type="text" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('bank_name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="is_active" value="Ketersediaan" />
                        <select id="is_active" name="is_active" class="mt-1 block w-full rounded border-gray-300 shadow-sm" required>
                            <option value="1">Ya</option>
                            <option value="0">Tidak</option>
                        </select>
                        <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                    </div>
                    {{-- <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" class="mt-1 block w-full rounded border-gray-300 shadow-sm" required>
                            <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div> --}}
            </div>
            </div>
            <div class="mt-6">
                <x-input-label for="notes" value="Catatan" />
                <textarea id="notes" name="notes" rows="3"
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm">{{ old('notes') }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>
            <x-primary-button class="mt-6">Simpan</x-primary-button>
        </form>
    </main>
</x-app-layout>
