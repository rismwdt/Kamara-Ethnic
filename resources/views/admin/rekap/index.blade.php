<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800">Rekap & Prioritas Pengisi Acara (Greedy)</h2>
  </x-slot>

  @php
    $totalPerformer   = $totalPerformer   ?? 0;
    $prioritasUtama   = $prioritasUtama   ?? '-';
    $kapasitasHariIni = $kapasitasHariIni ?? 0;
    $totalKapasitas   = $totalKapasitas   ?? 0;
    $avgDurasi        = $avgDurasi        ?? 0;
    $rekap            = collect($rekap ?? []);
  @endphp

  <div class="p-6 space-y-6">
    {{-- Cards --}}
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
        <p class="text-2xl font-bold text-green-600">{{ $kapasitasHariIni }}/{{ $totalKapasitas }}</p>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-sm text-gray-500">Rata-rata Durasi</p>
        <p class="text-2xl font-bold">{{ $avgDurasi }} jam</p>
      </div>
    </div>

    {{-- Tabel Prioritas --}}
    <div class="bg-white p-4 rounded-lg shadow overflow-x-auto">
      <table class="min-w-full text-sm text-left border border-gray-200">
        <thead class="bg-gray-100 text-gray-700 uppercase">
  <tr>
    <th class="px-4 py-2 border">No</th>
    <th class="px-4 py-2 border">Pesanan</th>
    <th class="px-4 py-2 border">Peran Pengisi Acara</th>
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
          @forelse($rekap as $i => $item)
  <tr class="hover:bg-gray-50 align-top">
    <td class="px-4 py-2 border">{{ $i+1 }}</td>

    {{-- Pesanan: kode (tebal) + nama klien (kecil, abu) --}}
    <td class="px-4 py-2 border">
      <div class="font-semibold">{{ $item->kode }}</div>
      <div class="text-xs text-gray-500">{{ $item->klien }}</div>
    </td>

    {{-- Peran Pengisi Acara: bullet list --}}
    <td class="px-4 py-2 border">
      @php $roles = collect($item->roles_list ?? []); @endphp
      @if($roles->isNotEmpty())
        <ul class="list-disc list-inside">
          @foreach($roles as $li)
            <li>{{ $li }}</li>
          @endforeach
        </ul>
      @else
        —
      @endif
    </td>

    <td class="px-4 py-2 border">{{ $item->deadline }}</td>
    <td class="px-4 py-2 border">{{ $item->value }}</td>
    <td class="px-4 py-2 border">{{ $item->complexity }}</td>
    <td class="px-4 py-2 border">{{ $item->customer }}</td>
    <td class="px-4 py-2 border">{{ $item->time }}</td>
    <td class="px-4 py-2 border font-bold">{{ $item->priority_score }}</td>

    {{-- Status: bullet list --}}
    <td class="px-4 py-2 border">
      @php $stats = collect($item->status_list ?? []); @endphp
      @if($stats->isNotEmpty())
        <ul class="list-disc list-inside">
          @foreach($stats as $li)
            <li>{{ $li }}</li>
          @endforeach
        </ul>
      @else
        —
      @endif
    </td>
  </tr>
@empty
  <tr><td colspan="10" class="px-4 py-6 text-center text-gray-500">Belum ada data untuk direkap.</td></tr>
@endforelse

        </tbody>
      </table>
    </div>
  </div>
</x-app-layout>
