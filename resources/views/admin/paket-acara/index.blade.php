<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Paket Acara') }}
        </h2>
    </x-slot>

    <main class="flex-1 mb-auto bg-white min-h-screen p-6 text-gray-900 flex flex-col">
        @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded">
            {{ session('success') }}
        </div>
        @endif
        <div class="flex justify-between items-center mb-4">
            <x-add-button href="{{ route('paket-acara.create') }}" label="Tambah Paket Acara" />
        </div>
        <x-table>
            <x-slot name="thead">
                <tr>
                    <th class="px-4 py-2">No.</th>
                    <th class="px-4 py-2">Gambar</th>
                    <th class="px-4 py-2">Nama Paket</th>
                    <th class="px-4 py-2">Jenis</th>
                    <th class="px-4 py-2">Durasi (jam)</th>
                    <th class="px-4 py-2">Harga</th>
                    {{-- <th class="px-4 py-2">Pengisi Acara</th> --}}
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Deskripsi</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </x-slot>
            @foreach ($events as $index => $event)
            <tr>
                <td class="px-4 py-2">{{ $index + 1 }}</td>
                <td class="px-4 py-2">
                    @if ($event->image)
                    <img src="{{ asset('storage/' . $event->image) }}" alt="Gambar"
                        class="w-16 h-16 object-cover rounded">
                    @else
                    <span class="text-sm text-gray-500">Tidak ada</span>
                    @endif
                </td>
                <td class="px-4 py-2">{{ $event->name }}</td>
                <td class="px-4 py-2 capitalize">{{ $event->type }}</td>
                <td class="px-4 py-2">{{ $event->duration }}</td>
                <td class="px-4 py-2">Rp{{ number_format($event->price, 0, ',', '.') }}</td>
                <td class="px-4 py-2">
                    @if ($event->status === 'aktif')
                    <span class="inline-block bg-green-100 text-green-800 text-sm px-2 py-1 rounded font-medium">
                        Aktif
                    </span>
                    @else
                    <span class="inline-block bg-red-100 text-red-800 text-sm px-2 py-1 rounded font-medium">
                        Nonaktif
                    </span>
                    @endif
                </td>
                <td class="px-4 py-2">{{ $event->description ?? '-' }}</td>
                <td class="px-4 py-2">
                    <div class="flex justify-center items-center space-x-2">
                        <a href="{{ route('paket-acara.edit', $event->id) }}">
                            <x-primary-button class="text-xs px-2 py-1">
                                <i class="fas fa-edit"></i>
                            </x-primary-button>
                        </a>
                        <x-danger-button type="button"
                            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'delete-{{ $event->id }}' }))">
                            <i class="fas fa-trash"></i>
                        </x-danger-button>
                        <x-modal-delete name="delete-{{ $event->id }}" :itemId="$event->id" :itemName="$event->name"
                            route="paket-acara.destroy" />
                    </div>
                </td>
            </tr>
            @endforeach
        </x-table>
    </main>
</x-app-layout>
