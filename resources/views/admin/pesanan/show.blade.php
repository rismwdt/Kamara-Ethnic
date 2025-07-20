<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Pesanan:') }} {{ $booking->booking_code }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-4">
            <a href="{{ route('pesanan.index') }}">
                <x-secondary-button>
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </x-secondary-button>
            </a>
        </div>
        <div class="bg-white shadow rounded-lg p-6 text-gray-800">
            <table class="table-auto w-full text-sm text-left text-gray-700">
                <tbody class="divide-y divide-gray-200">
                    <tr>
                        <th class="py-2 pr-4 font-medium w-1/3">Kode Pesanan</th>
                        <td>: {{ $booking->booking_code }}</td>
                    </tr>
                    <tr>
                        <th class="py-2 pr-4 font-medium">Paket Acara</th>
                        <td>: {{ $booking->event->name }}</td>
                    </tr>
                    <tr>
                        <th class="py-2 pr-4 font-medium">Nama Klien</th>
                        <td>: {{ $booking->client_name }}</td>
                    </tr>
                    <tr>
                        <th class="py-2 pr-4 font-medium">No. HP</th>
                        <td>: {{ $booking->phone }}</td>
                    </tr>
                    <tr>
                        <th class="py-2 pr-4 font-medium">Email</th>
                        <td>: {{ $booking->email }}</td>
                    </tr>
                    <tr>
                        <th class="py-2 pr-4 font-medium">Orang Tua Pria</th>
                        <td>: {{ $booking->male_parents }}</td>
                    </tr>
                    <tr>
                        <th class="py-2 pr-4 font-medium">Orang Tua Wanita</th>
                        <td>: {{ $booking->female_parents }}</td>
                    </tr>
                    <tr>
                        <th class="py-2 pr-4 font-medium">Nuansa Acara</th>
                        <td>: {{ $booking->nuance }}</td>
                    </tr>
                    <tr>
                        <th class="py-2 pr-4 font-medium">Tanggal</th>
                        <td>: {{ \Carbon\Carbon::parse($booking->date)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <th class="py-2 pr-4 font-medium">Waktu</th>
                        <td>: {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</td>
                    </tr>
                    <tr>
                        <th class="py-2 pr-4 font-medium">Alamat Lengkap</th>
                        <td>: {{ $booking->location_detail }}</td>
                    </tr>
                    <tr>
                        <th class="py-2 pr-4 font-medium">Catatan</th>
                        <td>: {{ $booking->notes }}</td>
                    </tr>
                    <tr>
                        <th class="py-2 pr-4 font-medium">Status</th>
                        <td>:
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
                    </tr>
                    <tr>
                        <th class="py-2 pr-4 font-medium">Pengisi Acara</th>
                        <td>
                            @if ($booking->performers->count())
                            <ul class="list-disc list-inside space-y-1 mt-1">
                                @foreach ($booking->performers as $performer)
                                <li>{{ $performer->name }}</li>
                                @endforeach
                            </ul>
                            @else
                            <span class="text-gray-500">Tidak ada</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="py-2 pr-4 font-medium">Bukti TF</th>
                        <td>:
                            @if($booking->image)
                            <img src="{{ asset('storage/' . $booking->image) }}" class="w-48 rounded shadow mt-2">
                            @else
                            <span class="text-gray-500">Tidak ada gambar</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
