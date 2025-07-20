<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pengisi Acara') }}
        </h2>
    </x-slot>

    <main class="flex-1 mb-auto bg-white min-h-screen p-6 text-gray-900 flex flex-col">
        @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded">
            {{ session('success') }}
        </div>
        @endif
        <div class="flex justify-between items-center mb-4">
            <x-add-button href="{{ route('pengisi-acara.create') }}" label="Tambah Pengisi Acara" />
        </div>
        <div class="overflow-x-auto w-full">
            <x-table>
                <x-slot name="thead">
                    <tr>
                        <th class="px-4 py-2">No.</th>
                        <th class="px-4 py-2">Nama</th>
                        <th class="px-4 py-2">Jenis Kelamin</th>
                        <th class="px-4 py-2">Peran</th>
                        <th class="px-4 py-2">No. HP</th>
                        <th class="px-4 py-2">Rekening</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Catatan</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </x-slot>
                @foreach ($performers as $index => $performer)
                <tr>
                    <td class="px-4 py-2">{{ $index + 1 }}</td>
                    <td class="px-4 py-2">{{ $performer->name }}</td>
                    <td class="px-4 py-2 capitalize">{{ $performer->gender }}</td>
                    <td class="px-4 py-2 capitalize">{{ $performer->category }}</td>
                    <td class="px-4 py-2">
                        @if ($performer->phone)
                        <a href="https://wa.me/{{ ltrim($performer->phone, '0') }}" target="_blank"
                            class="text-blue-600 underline">
                            {{ $performer->phone }}
                        </a>
                        @else
                        -
                        @endif
                    </td>
                    <td class="px-4 py-2">{{ $performer->account_number }} ({{ $performer->bank_name }})</td>
                    <td class="px-4 py-2">
                        @if ($performer->status === 'aktif')
                        <span class="inline-block bg-green-100 text-green-800 text-sm px-2 py-1 rounded font-medium">
                            Aktif
                        </span>
                        @else
                        <span class="inline-block bg-red-100 text-red-800 text-sm px-2 py-1 rounded font-medium">
                            Nonaktif
                        </span>
                        @endif
                    </td>
                    <td class="px-4 py-2">{{ $performer->notes ?? '-' }}</td>
                    <td class="px-4 py-2">
                        <div class="flex justify-center items-center space-x-2">
                            <a href="{{ route('pengisi-acara.edit', $performer->id) }}">
                                <x-primary-button class="text-xs px-2 py-1">
                                    <i class="fas fa-edit"></i>
                                </x-primary-button>
                            </a>
                            <x-danger-button type="button"
                                onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'delete-{{ $performer->id }}' }))">
                                <i class="fas fa-trash"></i>
                            </x-danger-button>
                            <x-modal-delete name="delete-{{ $performer->id }}" :itemId="$performer->id"
                                :itemName="$performer->name" route="pengisi-acara.destroy" />
                        </div>
                    </td>
                </tr>
                @endforeach
            </x-table>
        </div>
    </main>
</x-app-layout>
