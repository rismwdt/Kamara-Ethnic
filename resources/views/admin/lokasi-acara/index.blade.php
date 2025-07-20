<x-app-layout>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Lokasi Acara') }}
        </h2>
    </x-slot>

    <main class="flex-1 mb-auto bg-white min-h-screen p-6 text-gray-900 flex flex-col">
        @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded">
            {{ session('success') }}
        </div>
        @endif
        <div class="flex justify-between items-center mb-4">
            <button type="button"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-300 active:bg-blue-800 transition"
                onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'tambah-lokasi' }))">
                + Tambah Lokasi
            </button>
            <button type="button"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-300 active:bg-blue-800 transition"
                onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'tambah-estimasi' }))">
                + Tambah Estimasi
            </button>
        </div>
        <x-table>
            <x-slot name="thead">
                <tr>
                    <th class="px-4 py-2">No.</th>
                    <th class="px-4 py-2">Lokasi Pertama</th>
                    <th class="px-4 py-2">Lokasi Kedua</th>
                    <th class="px-4 py-2">Jarak (km)</th>
                    <th class="px-4 py-2">Estimasi (menit)</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </x-slot>
            @foreach ($estimasiList as $i => $estimasi)
            <tr class="border-b">
                <td class="px-4 py-2">{{ $i + 1 }}</td>
                <td class="px-4 py-2">
                    {{ $estimasi->fromLocation->name }}<br>
                    <span class="text-sm text-gray-500">{{ $estimasi->fromLocation->full_address }}</span>
                </td>
                <td class="px-4 py-2">
                    {{ $estimasi->toLocation->name }}<br>
                    <span class="text-sm text-gray-500">{{ $estimasi->toLocation->full_address }}</span>
                </td>
                <td class="px-4 py-2">{{ $estimasi->distance_km }}</td>
                <td class="px-4 py-2">{{ $estimasi->estimated_mnt }}</td>
                <td class="px-4 py-2">
                    <div class="flex justify-center items-center space-x-2">
                        <x-primary-button type="button"
                            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'edit-estimasi-{{ $estimasi->id }}' }))">
                            <i class="fas fa-edit"></i>
                        </x-primary-button>
                        <x-danger-button type="button"
                            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'delete-{{ $estimasi->id }}' }))">
                            <i class="fas fa-trash"></i>
                        </x-danger-button>
                        <x-modal-delete name="delete-{{ $estimasi->id }}" :itemId="$estimasi->id"
                            :itemName="$estimasi->name" route="lokasi-acara.destroy" />
                    </div>
                </td>
            </tr>
            @include('admin.lokasi-acara.modal-lokasi', ['estimate' => $estimasi, 'locations' => $locations])
            @endforeach
        </x-table>
    </main>
</x-app-layout>
