<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Paket Acara') }}
        </h2>
    </x-slot>

    <main class="flex-1 mb-auto bg-white min-h-screen p-6 text-gray-900 flex flex-col">
        <div class="flex justify-between items-center mb-4">
            <a href="{{ route('paket-acara.index') }}">
                <x-secondary-button>
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </x-secondary-button>
            </a>
        </div>
        <form method="POST" action="{{ route('paket-acara.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="flex gap-x-6">
                <div class="w-1/2 space-y-4">
                    <div>
                        <x-input-label for="name" value="Nama Paket" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required value="{{ old('name') }}"/>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                </div>
                <div class="w-1/2 space-y-4">
                    <div>
                        <x-input-label for="price" value="Harga" />
                        <x-text-input id="price" name="price" type="number" step="0.01" class="mt-1 block w-full" required value="{{ old('price') }}"/>
                        <x-input-error :messages="$errors->get('price')" class="mt-2" />
                    </div>
                </div>
            </div>
            <div class="flex gap-x-6 mt-4">
                    <div class="w-1/2">
                        <x-input-label for="type" value="Jenis Acara" />
                        <select id="type" name="type" class="mt-1 block w-full rounded border-gray-300 shadow-sm" required>
                            <option value="">Pilih Jenis Acara</option>
                            <option value="siraman" {{ old('type') == 'siraman' ? 'selected' : '' }}>Siraman</option>
                            <option value="upacara_adat" {{ old('type') == 'upacara_adat' ? 'selected' : '' }}>Upacara Adat</option>
                            <option value="sisingaan" {{ old('type') == 'sisingaan' ? 'selected' : '' }}>Sisingaan</option>
                            <option value="lainnya" {{ old('type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>
                    <div class="w-1/2">
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" class="mt-1 block w-full rounded border-gray-300 shadow-sm" required>
                            <option value="aktif" {{ old('type') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('type') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>
                </div>
            <div class="flex gap-x-6 mt-6">
                <div class="w-1/2">
                    <x-input-label for="description" value="Deskripsi" />
                    <textarea id="description" name="description" rows="5"
                        class="mt-1 block w-full rounded border border-gray-300 shadow-sm">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
                <div class="w-1/2">
                    <x-input-label for="image" value="Gambar" />
                    <input
                        type="file"
                        name="image"
                        id="image"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm text-sm text-gray-900 file:border-0
                        file:mr-4 file:py-2 file:px-4 file:bg-gray-100 file:text-gray-700 file:rounded-md file:cursor-pointer" required/>
                    <x-input-error :messages="$errors->get('image')" class="mt-2" />
                </div>
            </div>
            <x-primary-button class="mt-6">SIMPAN</x-primary-button>
        </form>
    </main>
</x-app-layout>
