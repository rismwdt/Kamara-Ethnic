<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Rekomendasi Jadwal') }}
        </h2>
    </x-slot>

    <main class="flex-1 mb-auto bg-white min-h-screen p-6 text-gray-900 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">
                Rekomendasi Jadwal Tanggal:
                <form action="{{ route('admin.pesanan.rekomendasi') }}" method="GET" class="inline">
                    <select name="tanggal" onchange="this.form.submit()" class="border rounded px-2 py-1 ml-2 w-60">
                        @foreach ($availableDates as $date)
                            <option value="{{ $date }}" {{ $date == $tanggal ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </h2>
            <a href="{{ route('pesanan.index') }}">
                <x-secondary-button>
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </x-secondary-button>
            </a>
        </div>


        {{-- Bagian Rekomendasi --}}
        <h3 class="text-lg font-bold mb-2 text-green-600">✅ Jadwal yang Direkomendasikan</h3>
        @if(count($recommended))
        {{-- <pre>{{ print_r($recommended->toArray(), true) }}</pre> --}}
        <x-table>
            <x-slot name="thead">
                <th>Kode</th>
                <th>Klien</th>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Lokasi</th>
            </x-slot>
                @foreach ($recommended as $item)
                <tr>
                    <td>{{ $item->booking_code }}</td>
                    <td>{{ $item->client_name }}</td>
                    <td>{{ $item->start_time }}</td>
                    <td>{{ $item->end_time }}</td>
                    <td>{{ $item->location_detail }}</td>
                </tr>
                @endforeach
        </x-table>
        @else
        <p class="text-gray-500">Tidak ada pesanan yang direkomendasikan hari ini.</p>
        @endif

        {{-- Bagian Ditolak --}}
        <h3 class="text-lg font-bold mt-10 mb-2 text-red-600">❌ Jadwal yang Ditolak (Tumpang Tindih)</h3>
        @if(count($rejected))
        <x-table>
            <x-slot name="thead">
                <th>Kode</th>
                <th>Klien</th>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Lokasi</th>
            </x-slot>
                @foreach ($rejected as $item)
                <tr>
                    <td>{{ $item->booking_code }}</td>
                    <td>{{ $item->client_name }}</td>
                    <td>{{ $item->start_time }}</td>
                    <td>{{ $item->end_time }}</td>
                    <td>{{ $item->location_detail }}</td>
                </tr>
                @endforeach
        </x-table>
        @else
        <p class="text-gray-500">Tidak ada pesanan yang ditolak karena bentrok hari ini.</p>
        @endif
    </main>
</x-app-layout>
