<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rekap & Prioritas Pengisi Acara (Greedy Algorithm)') }}
        </h2>
    </x-slot>

    <div class="p-6 space-y-6 bg-gray-50 min-h-screen">
        {{-- Ringkasan --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-gray-500">Total Performer</p>
                <p class="text-2xl font-bold">{{ $totalPerformer }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-gray-500">Prioritas Utama</p>
                <p class="text-2xl font-bold text-red-500">{{ $prioritasUtama }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-gray-500">Kapasitas Hari Ini</p>
                <p class="text-2xl font-bold text-green-500">{{ $kapasitasHariIni }}/{{ $totalKapasitas }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-gray-500">Rata-rata Durasi</p>
                <p class="text-2xl font-bold">{{ $avgDurasi }} jam</p>
            </div>
        </div>

        {{-- Penjelasan Greedy Score --}}
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="font-bold mb-4">Greedy Algorithm Priority Calculation</h3>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-center">
                <div>
                    <p class="text-red-500 font-bold">Deadline Score</p>
                    <p class="text-sm text-gray-500">40% weight<br>Performer terdekat jadwal tampil</p>
                </div>
                <div>
                    <p class="text-green-500 font-bold">Value Score</p>
                    <p class="text-sm text-gray-500">25% weight<br>Performer bernilai tinggi</p>
                </div>
                <div>
                    <p class="text-blue-500 font-bold">Complexity Score</p>
                    <p class="text-sm text-gray-500">20% weight<br>Penampilan sederhana lebih dulu</p>
                </div>
                <div>
                    <p class="text-purple-500 font-bold">Customer Score</p>
                    <p class="text-sm text-gray-500">10% weight<br>VIP / langganan</p>
                </div>
                <div>
                    <p class="text-yellow-500 font-bold">Time Score</p>
                    <p class="text-sm text-gray-500">5% weight<br>FIFO tie-breaker</p>
                </div>
            </div>
        </div>

        {{-- Tabel Hasil Greedy --}}
        <div class="bg-white p-4 rounded-lg shadow overflow-x-auto">
            <table class="min-w-full text-sm text-left border border-gray-200">
                <thead class="bg-gray-100 text-gray-700 uppercase">
                    <tr>
                        <th class="px-4 py-2 border">No</th>
                        <th class="px-4 py-2 border">Nama Performer</th>
                        <th class="px-4 py-2 border">Deadline</th>
                        <th class="px-4 py-2 border">Value</th>
                        <th class="px-4 py-2 border">Complexity</th>
                        <th class="px-4 py-2 border">Customer</th>
                        <th class="px-4 py-2 border">Time</th>
                        <th class="px-4 py-2 border">Priority Score</th>
                        <th class="px-4 py-2 border">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekap as $i => $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border">{{ $i+1 }}</td>
                            <td class="px-4 py-2 border">{{ $item->nama }}</td>
                            <td class="px-4 py-2 border">{{ $item->deadline }}</td>
                            <td class="px-4 py-2 border">{{ $item->value }}</td>
                            <td class="px-4 py-2 border">{{ $item->complexity }}</td>
                            <td class="px-4 py-2 border">{{ $item->customer }}</td>
                            <td class="px-4 py-2 border">{{ $item->time }}</td>
                            <td class="px-4 py-2 border font-bold">{{ $item->priority_score }}</td>
                            <td class="px-4 py-2 border">{{ $item->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
