<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <main class="flex-1 mb-auto bg-white  p-6 text-gray-900 flex flex-col">
        <!-- Top Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-6 mb-6">
            <!-- Card 1: Total Pendapatan -->
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
                <div class="text-xs text-gray-500 font-semibold mb-2">Total Pendapatan</div>
                <div class="text-2xl font-bold mb-1">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                <div class="text-sm text-indigo-600">Pendapatan Bulan Ini: Rp
                    {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</div>
                {{-- <button class="block mx-auto bg-gray-200 text-gray-700 text-xs font-semibold rounded-md px-3 py-1 hover:bg-gray-300">
                    Detail
                </button> --}}
            </div>
            <!-- Card 2: Jadwal Acara -->
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
                <div class="text-xs text-gray-500 font-semibold mb-2">Total Jadwal Acara</div>
                <div class="text-2xl font-bold mb-1">{{ $totalJadwal }} Jadwal</div>
                <div class="text-sm text-indigo-600">Bulan Ini: {{ $jadwalBulanIni }} Jadwal </div>
                {{-- <button class="block mx-auto bg-gray-200 text-gray-700 text-xs font-semibold rounded-md px-3 py-1 hover:bg-gray-300">
                    Detail
                </button> --}}
            </div>
            <!-- Card 3: Klien -->
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
                <div class="text-xs text-gray-500 font-semibold mb-2">Total Klien</div>
                <div class="text-2xl font-bold mb-1">{{ $jumlahKlien }} Klien</div>
                <div class="text-sm text-indigo-600">Bulain Ini: {{ $jumlahKlienBulanIni }} Klien </div>
                {{-- <button class="block mx-auto bg-gray-200 text-gray-700 text-xs font-semibold rounded-md px-3 py-1 hover:bg-gray-300">
                    Detail
                </button> --}}
            </div>
        </div>
        <!-- Bottom Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-6">
            <!-- Kalender Acara -->
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
                <h2 class="font-semibold text-gray-900 mb-2 text-sm">Kalender Acara</h2>
                <div class="text-xs font-semibold text-gray-600 mb-3 flex justify-between items-center">
                    <span>{{ now()->translatedFormat('F Y') }}</span>
                </div>
                <div class="grid grid-cols-7 gap-1 text-center text-xs font-semibold text-gray-500 mb-2">
                    <div>SEN</div>
                    <div>SEL</div>
                    <div>RAB</div>
                    <div>KAM</div>
                    <div>JUM</div>
                    <div>SAB</div>
                    <div>MIN</div>
                </div>
                @php
                $startOfMonth = now()->startOfMonth();
                $endOfMonth = now()->endOfMonth();
                $startDay = $startOfMonth->copy()->startOfWeek();
                $endDay = $endOfMonth->copy()->endOfWeek();
                $loopDate = $startDay->copy();
                @endphp
                <div class="grid grid-cols-7 gap-1 text-center text-xs font-semibold">
                    @while ($loopDate <= $endDay) @php $tanggal=$loopDate->format('Y-m-d');
                        $isEvent = in_array($tanggal, $tanggalDenganAcara->toArray());
                        $isCurrentMonth = $loopDate->month === now()->month;
                        @endphp
                        <div class="py-1
            {{ $isEvent ? 'bg-indigo-500 text-white rounded-full' : '' }}
            {{ !$isCurrentMonth ? 'text-gray-300' : '' }}">
                            {{ $loopDate->day }}
                        </div>
                        @php $loopDate->addDay(); @endphp
                        @endwhile
                </div>
            </div>
            <!-- Jadwal Minggu Ini -->
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 md:col-span-2 lg:col-span-2">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-900 text-sm">Jadwal Minggu Ini</h3>
                    <a href="{{ route('pesanan.index') }}" class="bg-indigo-600 text-white text-xs font-semibold rounded-md px-3 py-1 hover:bg-gray-300 ml-auto">
                        Detail
                    </a>
                </div>
                <table class="w-full text-xs text-left text-gray-600 font-semibold">
                    <thead>
                        <tr>
                            <th class="pb-3">No</th>
                            <th class="pb-3">Tanggal</th>
                            <th class="pb-3">Nama Klien</th>
                            <th class="pb-3">Paket Acara</th>
                            <th class="pb-3">Harga</th>
                            <th class="pb-3">Alamat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jadwalMingguIni as $index => $booking)
                        <tr class="border-t border-gray-200">
                            <td class="py-2">{{ $index + 1 }}</td>
                            <td class="py-2">{{ $booking->date }}</td>
                            <td class="py-2">{{ $booking->client_name }}</td>
                            <td class="py-2">{{ $booking->event->name }}</td>
                            <td class="py-2">Rp {{ number_format($booking->event->price, 0, ',', '.') }}</td>
                            <td class="py-2">{{ $booking->location_detail }}</td>
                        </tr>
                        @empty
                        <tr class="border-t border-gray-200">
                            <td colspan="4" class="py-2 text-center text-gray-500">Tidak ada jadwal minggu ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</x-app-layout>
