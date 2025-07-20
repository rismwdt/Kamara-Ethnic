<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pesanan') }}
        </h2>
    </x-slot>

    <main class="flex-1 mb-auto bg-white min-h-screen p-6 text-gray-900 flex flex-col">
        @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded">
            {{ session('success') }}
        </div>
        @endif
        <div class="flex justify-between items-center mb-4">
            {{-- <form action="{{ route('pesanan.export') }}" method="GET"> --}}
            <x-primary-button type="submit">
                <i class="fas fa-download mr-1"></i> Unduh Laporan
            </x-primary-button>
            </form>
        </div>
        <x-table>
            <x-slot name="thead">
                <tr>
                    <th class="px-4 py-2">No.</th>
                    <th class="px-4 py-2">Nama Klien</th>
                    {{-- <th class="px-4 py-2">No. Hp</th> --}}
                    <th class="px-4 py-2">Paket</th>
                    <th class="px-4 py-2">Tanggal</th>
                    <th class="px-4 py-2">Waktu</th>
                    <th class="px-4 py-2">Alamat Lengkap</th>
                    <th class="px-4 py-2">Pengisi Acara</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </x-slot>
            @foreach ($bookings as $index => $booking)
            <tr>
                <td class="px-4 py-2">{{ $index + 1 }}</td>
                <td class="px-4 py-2">{{ $booking->client_name }}</td>
                {{-- <td class="px-4 py-2">{{ $booking->phone }}</td> --}}
                <td class="px-4 py-2">{{ $booking->event->name }}</td>
                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($booking->date)->format('d-m-Y') }}</td>
                <td class="px-4 py-2">
                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}
                    -
                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                </td>
                <td class="px-4 py-2">{{ $booking->location_detail }}</td>
                <td class="-px-4 py-2 max-w-md">
                    @if ($booking->performers->count())
                    <ul class="list-disc list-inside space-y-1 max-h-24 overflow-y-auto pr-1 text-sm">
                        @foreach ($booking->performers as $performer)
                        <li class="break-words">{{ $performer->name }}</li>
                        @endforeach
                    </ul>
                    @else
                    <span class="text-gray-500">-</span>
                    @endif
                </td>
                <td class="px-4 py-2">
                    @php
                    $statusColor = match($booking->status) {
                    'tertunda' => 'bg-yellow-100 text-yellow-800',
                    'diterima' => 'bg-green-100 text-green-800',
                    'ditolak' => 'bg-red-100 text-red-800',
                    'selesai' => 'bg-indigo-100 text-indigo-800',
                    default => 'bg-gray-100 text-gray-800',
                    };
                    @endphp
                    <span class="px-2 py-1 text-xs font-semibold rounded {{ $statusColor }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </td>
                <td class="px-4 py-2">
                    <div class="flex justify-center items-center space-x-2">
                        <a href="{{ route('admin.pesanan.show', $booking->id) }}">
                            <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                                <i class="fas fa-eye ml-1"></i>
                            </x-primary-button>
                        </a>
                        <a href="{{ route('pesanan.edit', $booking->id) }}">
                            <x-primary-button class="text-xs px-2 py-1">
                                <i class="fas fa-edit"></i>
                            </x-primary-button>
                        </a>
                        <x-danger-button type="button"
                            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'delete-{{ $booking->id }}' }))">
                            <i class="fas fa-trash"></i>
                        </x-danger-button>
                        <x-modal-delete name="delete-{{ $booking->id }}" :itemId="$booking->id"
                            :itemName="$booking->booking_code" route="pesanan.destroy" />
                    </div>
                </td>
            </tr>
            @endforeach
        </x-table>
    </main>
</x-app-layout>
