<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <main class="flex-1 mb-auto bg-white  p-6 text-gray-900 flex flex-col">
        @php
            $notifications = auth()->user()->unreadNotifications;
        @endphp

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
            <div
                x-data="{
                    month: {{ now()->month - 1 }},
                    year: {{ now()->year }},
                    tanggalDenganAcara: @js($tanggalDenganAcara),
                    getCurrentMonthName() {
                        const bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                        return bulan[this.month] + ' ' + this.year;
                    },
                    prevMonth() {
                        if (this.month === 0) {
                            this.month = 11;
                            this.year--;
                        } else {
                            this.month--;
                        }
                    },
                    nextMonth() {
                        if (this.month === 11) {
                            this.month = 0;
                            this.year++;
                        } else {
                            this.month++;
                        }
                    }
                }"
                class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
            <h3 class="font-semibold text-gray-900 text-md">Kalender Acara</h3>
                <div class="flex items-center justify-between mb-4 text-sm text-gray-700 font-medium">
                    {{-- <button @click="prevMonth" class="hover:text-black">←</button> --}}
                    <span x-text="getCurrentMonthName()" class="text-sm font-semibold"></span>
                    {{-- <button @click="nextMonth" class="hover:text-black">→</button> --}}
                </div>

                <div class="grid grid-cols-7 gap-1 text-center text-xs font-semibold text-gray-500 mb-2">
                    <div>Min</div>
                    <div>Sen</div>
                    <div>Sel</div>
                    <div>Rab</div>
                    <div>Kam</div>
                    <div>Jum</div>
                    <div>Sab</div>
                </div>

                @php
                    $carbon = \Carbon\Carbon::now();
                    $currentMonth = $carbon->copy()->startOfMonth();
                    $startOfGrid = $currentMonth->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
                    $endOfGrid = $carbon->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);
                    $loopDate = $startOfGrid->copy();
                @endphp

                <div class="grid grid-cols-7 gap-1 text-center text-xs">
                    @while ($loopDate <= $endOfGrid)
                        @php
                            $tanggal = $loopDate->format('Y-m-d');
                            $jumlah = $tanggalDenganAcara[$tanggal] ?? 0;

                            $isCurrentMonth = $loopDate->month === $currentMonth->month;
                            $baseClass = $isCurrentMonth ? 'text-gray-800' : 'text-gray-300';

                            $bgClass = '';
                            if ($jumlah >= 5) {
                                $bgClass = 'border border-red-300 bg-red-100 text-red-600 rounded-lg';
                            } elseif ($jumlah >= 1) {
                                $bgClass = 'border border-indigo-300 bg-indigo-100 text-indigo-600 rounded-lg';
                            }

                            $showAcara = $jumlah > 0;
                        @endphp

                        <div class="p-1 flex flex-col items-center justify-center {{ $baseClass }} {{ $bgClass }}">
                            <div class="text-sm">{{ $loopDate->day }}</div>
                            @if($showAcara)
                                <div class="text-[10px] mt-1 {{ $jumlah >= 5 ? 'text-red-600' : 'text-indigo-600' }}">
                {{ $jumlah }} acara
            </div>
                            @endif
                        </div>

                        @php $loopDate->addDay(); @endphp
                    @endwhile
                </div>
            </div>
                        <!-- Jadwal Minggu Ini -->
                        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 md:col-span-2 lg:col-span-2">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-900 text-md">Jadwal Minggu Ini</h3>
                    <a href="{{ route('pesanan.index') }}" class="bg-indigo-600 text-white text-xs font-semibold rounded-md px-3 py-1 hover:bg-gray-300 ml-auto">
                        Detail
                    </a>
                </div>
                <table class="w-full text-xs text-left text-gray-600 font-semibold">
                <thead>
                    <tr class="border-b border-gray-300">
                        <th class="pb-3 px-2">No</th>
                        <th class="pb-3 px-3">Tanggal</th>
                        <th class="pb-3 px-3">Waktu</th>
                        <th class="pb-3 px-3">Nama Klien</th>
                        <th class="pb-3 px-3">Paket Acara</th>
                        <th class="pb-3 px-3">Alamat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jadwalMingguIni as $index => $booking)
                        <tr class="border-t border-gray-200">
                            <td class="py-2 px-3">{{ $index + 1 }}</td>
                            <td class="py-2 px-3">{{ \Carbon\Carbon::parse($booking->date)->format('d-m-Y') }}</td>
                            <td class="py-2 px-3">
                                {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                            </td>
                            <td class="py-2 px-3">{{ $booking->client_name }}</td>
                            <td class="py-2 px-3">{{ $booking->event->name }}</td>
                            <td class="py-2 px-3">{{ $booking->location_detail }}</td>
                        </tr>
                    @empty
                        <tr class="border-t border-gray-200">
                            <td colspan="6" class="py-2 px-3 text-center text-gray-500">
                                Tidak ada jadwal minggu ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </main>
</x-app-layout>
