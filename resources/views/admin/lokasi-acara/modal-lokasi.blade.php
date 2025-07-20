{{-- Modal Tambah Lokasi --}}
<x-modal name="tambah-lokasi">
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-md font-semibold text-gray-800 dark:text-gray-100">Tambah Lokasi</h2>
            <button
                @click="$dispatch('close-modal', 'tambah-lokasi')"
                class="text-gray-500 hover:text-red-600 text-3xl">
                &times;
            </button>
        </div>
        @if ($errors->any())
            <div class="mb-4 text-sm text-red-600">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('lokasi-acara.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <x-input-label for="nama" value="Nama Lokasi" />
                <x-text-input id="nama" name="name" type="text" class="mt-1 block w-full" required />
            </div>
            <div class="mb-4">
                <x-input-label for="alamat" value="Alamat" />
                <select id="alamatSelect" class="w-full mb-3"
                    onchange="document.getElementById('alamatInput').value = this.value">
                    <option value="">-- Pilih Alamat --</option>
                    @foreach ($alamatList as $alamat)
                        <option value="{{ $alamat }}">{{ $alamat }}</option>
                    @endforeach
                </select>
                <textarea id="alamatInput" name="full_address" rows="3"
                    class="w-full border px-3 py-2 rounded-md text-sm resize-none focus:ring-1 focus:ring-primary"
                    placeholder="Masukkan atau edit alamat lokasi acara" required></textarea>
            </div>
            <div class="text-right">
                <x-primary-button type="submit">
                    Simpan Lokasi
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>

{{-- Modal Tambah Estimasi --}}
<x-modal name="tambah-estimasi">
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-md font-semibold text-gray-800 dark:text-gray-100">Tambah Estimasi</h2>
            <button @click="$dispatch('close-modal', 'tambah-estimasi')" class="text-gray-500 hover:text-red-600 text-3xl">
                &times;
            </button>
        </div>
        @if ($errors->any() && session('modal') === 'tambah-estimasi')
            <div class="mb-4 text-sm text-red-600">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('estimasi.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="from_location">Lokasi Pertama</label>
                    <select id="from_location" name="from_location_id" required class="w-full border rounded px-2 py-1">
                        <option value="">-- Pilih Alamat --</option>
                        @foreach ($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->full_address }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="to_location">Lokasi Kedua</label>
                    <select id="to_location" name="to_location_id" required class="w-full border rounded px-2 py-1">
                        <option value="">-- Pilih Alamat --</option>
                        @foreach ($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->full_address }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="mt-4">
                    <label for="distance_km">Jarak (km)</label>
                    <input type="number" step="0.01" name="distance_km" class="w-full border rounded px-2 py-1" required>
                </div>
                <div class="mt-4">
                    <label for="estimated_mnt">Estimasi Waktu (menit)</label>
                    <input type="number" name="estimated_mnt" class="w-full border rounded px-2 py-1" required>
                </div>
            </div>
            <div class="mt-4 text-right">
                <x-primary-button>Simpan</x-primary-button>
            </div>
        </form>
    </div>
</x-modal>

{{-- Modal Edit Estimasi --}}
@foreach ($estimasiList as $estimate)
    <x-modal name="edit-estimasi-{{ $estimate->id }}">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-md font-semibold text-gray-800 dark:text-gray-100">Edit Estimasi</h2>
                <button @click="$dispatch('close-modal', 'edit-estimasi-{{ $estimate->id }}')" class="text-gray-500 hover:text-red-600 text-3xl">
                    &times;
                </button>
            </div>
            <form action="{{ route('estimasi.update', $estimate->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label>Lokasi Pertama</label>
                        <select name="from_location_id" class="w-full border rounded px-2 py-1">
                            @foreach ($locations as $loc)
                                <option value="{{ $loc->id }}" {{ $loc->id == $estimate->from_location_id ? 'selected' : '' }}>
                                    {{ $loc->full_address }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>Lokasi Kedua</label>
                        <select name="to_location_id" class="w-full border rounded px-2 py-1">
                            @foreach ($locations as $loc)
                                <option value="{{ $loc->id }}" {{ $loc->id == $estimate->to_location_id ? 'selected' : '' }}>
                                    {{ $loc->full_address }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <label>Jarak (km)</label>
                        <input type="number" step="0.1" name="distance_km" value="{{ $estimate->distance_km }}" class="w-full border rounded px-2 py-1">
                    </div>
                    <div>
                        <label>Estimasi Waktu (menit)</label>
                        <input type="number" name="estimated_mnt" value="{{ $estimate->estimated_mnt }}" class="w-full border rounded px-2 py-1">
                    </div>
                </div>
                <div class="mt-4 text-right">
                    <x-primary-button>Update</x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>
@endforeach



