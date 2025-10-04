<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <main class="flex-1 mb-auto bg-white dark:bg-gray-900 p-6 text-gray-900 dark:text-gray-100 flex flex-col">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold mb-2">Total Acara Keseluruhan</div>
                <div class="text-3xl font-extrabold text-gray-900 dark:text-white mb-1">
                    {{ (int)($totalAcara ?? 0) }} Acara
                </div>

                <div class="h-px bg-gray-200 dark:bg-gray-700 my-2"></div>

                <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold">Total Acara Minggu Ini</div>
                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ (int)($totalAcaraMingguIni ?? 0) }} Acara
                    <span class="text-gray-500 dark:text-gray-400 font-normal">
                        ({{ $weekLabelStart ?? '' }} – {{ $weekLabelEnd ?? '' }})
                    </span>
                </div>
            </div>

            <div x-data="calendarComp({
          initYear: {{ now()->year }},
          initMonthIndex: {{ now()->month - 1 }},
          countsByMonth: @js($countsByMonth)
        })" class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white text-md">Kalender Acara</h3>
                    <div class="flex items-center gap-2">
                        <button @click="prev()"
                            class="px-2 py-1 rounded border text-xs hover:bg-gray-50 dark:hover:bg-gray-700">‹</button>
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200"
                            x-text="monthNames[month] + ' ' + year"></span>
                        <button @click="next()"
                            class="px-2 py-1 rounded border text-xs hover:bg-gray-50 dark:hover:bg-gray-700">›</button>
                    </div>
                </div>

                <div
                    class="grid grid-cols-7 gap-1 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
                    <div>Min</div>
                    <div>Sen</div>
                    <div>Sel</div>
                    <div>Rab</div>
                    <div>Kam</div>
                    <div>Jum</div>
                    <div>Sab</div>
                </div>

                <div class="grid grid-cols-7 gap-1 text-center text-xs">
                    <template x-for="cell in days()" :key="cell.dstr">
                        <div class="p-1 flex flex-col items-center justify-center rounded-lg" :class="[
                cell.inCurrent ? 'text-gray-800 dark:text-gray-100' : 'text-gray-300 dark:text-gray-600',
                cell.count >= 5
                  ? 'border border-red-300 dark:border-red-700 bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300'
                  : (cell.count >= 1
                    ? 'border border-indigo-300 dark:border-indigo-700 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300'
                    : '')
              ]">
                            <div class="text-sm" x-text="cell.day"></div>
                            <div class="text-[10px] mt-1" x-show="cell.count > 0"
                                :class="cell.count >= 5 ? 'text-red-700 dark:text-red-300' : 'text-indigo-700 dark:text-indigo-300'">
                                <span x-text="cell.count"></span> acara
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white text-md">Jadwal Minggu Ini</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left text-gray-700 dark:text-gray-200 font-semibold">
                        <thead>
                            <tr class="border-b border-gray-300 dark:border-gray-700">
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
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td class="py-2 px-3">{{ $index + 1 }}</td>
                                <td class="py-2 px-3">
                                    {{ \Carbon\Carbon::parse($booking->date)->translatedFormat('d-m-Y') }}</td>
                                <td class="py-2 px-3">
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} –
                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                </td>
                                <td class="py-2 px-3">{{ $booking->client_name }}</td>
                                <td class="py-2 px-3">{{ optional($booking->event)->name ?? '—' }}</td>
                                <td class="py-2 px-3">{{ $booking->location_detail }}</td>
                            </tr>
                            @empty
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td colspan="6" class="py-2 px-3 text-center text-gray-500 dark:text-gray-400">
                                    Tidak ada jadwal minggu ini
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('alpine:init', () => {
            window.calendarComp = ({
                initYear,
                initMonthIndex,
                countsByMonth
            }) => ({
                year: initYear,
                month: initMonthIndex,
                countsByMonth,
                monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus',
                    'September', 'Oktober', 'November', 'Desember'
                ],

                ymKey() {
                    const m = String(this.month + 1).padStart(2, '0');
                    return `${this.year}-${m}`;
                },
                getCount(dateStr) {
                    const map = this.countsByMonth[this.ymKey()] || {};
                    return map[dateStr] ? ? 0;
                },

                startOfGrid() {
                    const d = new Date(this.year, this.month, 1);
                    const dow = d.getDay();
                    d.setDate(d.getDate() - dow);
                    return d;
                },
                endOfGrid() {
                    const d = new Date(this.year, this.month + 1, 0);
                    const dow = d.getDay();
                    d.setDate(d.getDate() + (6 - dow));
                    return d;
                },
                days() {
                    const out = [];
                    const start = this.startOfGrid();
                    const end = this.endOfGrid();
                    for (let dt = new Date(start); dt <= end; dt.setDate(dt.getDate() + 1)) {
                        const y = dt.getFullYear();
                        const m = String(dt.getMonth() + 1).padStart(2, '0');
                        const day = String(dt.getDate()).padStart(2, '0');
                        const dstr = `${y}-${m}-${day}`;
                        out.push({
                            d: new Date(dt),
                            dstr,
                            day: parseInt(day, 10),
                            inCurrent: dt.getMonth() === this.month,
                            count: this.getCount(dstr),
                        });
                    }
                    return out;
                },

                prev() {
                    if (this.month === 0) {
                        this.month = 11;
                        this.year--;
                    } else {
                        this.month--;
                    }
                },
                next() {
                    if (this.month === 11) {
                        this.month = 0;
                        this.year++;
                    } else {
                        this.month++;
                    }
                },
            });
        });
    </script>
</x-app-layout>
