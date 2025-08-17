<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800">Optimasi Jadwal (Greedy)</h2>
  </x-slot>

  @php
    $from = $from ?? now()->toDateString();
    $to   = $to   ?? now()->addDays(7)->toDateString();
    $weightsPct = $weightsPct ?? ['deadline'=>40,'value'=>25,'complexity'=>20,'customer'=>10,'time'=>5];
    $rekap = collect($rekap ?? []);
  @endphp

  <div class="p-6 space-y-4">
    {{-- Filter & Bobot --}}
    <form method="GET" class="bg-white p-4 rounded-lg shadow space-y-4">
  {{-- Baris 1: Filter tanggal --}}
  <div class="flex flex-wrap gap-4 items-end">
    <div>
      <label class="text-xs text-gray-500">Dari</label>
      <input type="date" name="from" value="{{ $from }}" class="border rounded px-2 py-1">
    </div>
    <div>
      <label class="text-xs text-gray-500">Sampai</label>
      <input type="date" name="to" value="{{ $to }}" class="border rounded px-2 py-1">
    </div>
  </div>

  {{-- Baris 2: Bobot --}}
  <div class="flex flex-wrap gap-4 items-end">
    <div>
      <label class="text-xs text-gray-500">Deadline %</label>
      <input type="number" min="0" max="100" name="w_deadline" value="{{ (int)$weightsPct['deadline'] }}" class="w-20 border rounded px-2 py-1">
    </div>
    <div>
      <label class="text-xs text-gray-500">Value %</label>
      <input type="number" min="0" max="100" name="w_value" value="{{ (int)$weightsPct['value'] }}" class="w-20 border rounded px-2 py-1">
    </div>
    <div>
      <label class="text-xs text-gray-500">Complexity %</label>
      <input type="number" min="0" max="100" name="w_complexity" value="{{ (int)$weightsPct['complexity'] }}" class="w-24 border rounded px-2 py-1">
    </div>
    <div>
      <label class="text-xs text-gray-500">Customer %</label>
      <input type="number" min="0" max="100" name="w_customer" value="{{ (int)$weightsPct['customer'] }}" class="w-24 border rounded px-2 py-1">
    </div>
    <div>
      <label class="text-xs text-gray-500">Time %</label>
      <input type="number" min="0" max="100" name="w_time" value="{{ (int)$weightsPct['time'] }}" class="w-20 border rounded px-2 py-1">
    </div>

    {{-- PENTING: jadikan submit --}}
    <x-primary-button type="submit" class="mt-6">Terapkan</x-primary-button>
  </div>
</form>

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
