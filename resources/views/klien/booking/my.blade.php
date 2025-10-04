@extends('layouts.client')

@section('title','Pesanan Saya')

@section('content')
<section id="orders" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 pb-20">
    <h1 class="text-2xl font-bold mb-6">Pesanan Saya</h1>

    @php
    $statusClass = fn($s) => match($s) {
    'tertunda' => 'px-2 py-1 text-xs font-semibold rounded bg-yellow-100 text-yellow-800',
    'diterima' => 'px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800',
    'ditolak' => 'px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800',
    'selesai' => 'px-2 py-1 text-xs font-semibold rounded bg-indigo-100 text-indigo-800',
    default => 'px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800',
    };
    @endphp

    <div class="md:hidden space-y-4">
        @forelse ($bookings as $b)
        <div class="rounded-xl border border-gray-200 p-4">
            <div class="text-sm text-gray-500">Kode Pesanan</div>
            <div class="font-semibold text-gray-900 mb-2">{{ $b->booking_code }}</div>

            <div class="text-sm text-gray-500">Tanggal Acara</div>
            <div class="mb-2">
                {{ \Illuminate\Support\Carbon::parse($b->date)->translatedFormat('d F Y') }}<br>
                <span class="text-gray-600">
                    {{ \Illuminate\Support\Carbon::parse($b->start_time)->format('H:i') }}
                    —
                    {{ \Illuminate\Support\Carbon::parse($b->end_time)->format('H:i') }}
                </span>
            </div>

            <div class="text-sm text-gray-500">Tanggal & Waktu Pesanan</div>
            <div class="mb-2 text-gray-700">
                {{ $b->created_at->translatedFormat('d F Y, H:i') }}
            </div>

            <div class="text-sm text-gray-500">Paket Acara</div>
            <div class="mb-3 text-gray-700">
                {{ optional($b->event)->name ?? 'Paket Acara' }}
            </div>

            <div class="flex items-center justify-between">
                <span class="{{ $statusClass($b->status) }}">{{ ucfirst($b->status) }}</span>
                <a href="{{ route('booking.invoice', $b) }}"
                    class="inline-flex items-center px-3 py-1.5 rounded-lg bg-primary text-white text-sm hover:bg-[#5a0c0f] transition font-semibold">
                    Lihat Invoice
                </a>
            </div>
        </div>
        @empty
        <div class="rounded-xl border border-gray-200 p-6 text-center text-gray-500">
            Belum ada pesanan.
        </div>
        @endforelse
    </div>

    <div class="hidden md:block overflow-x-auto rounded-xl border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Pesanan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Acara</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal & Waktu Pesanan
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paket Acara</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($bookings as $b)
                <tr>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $b->booking_code }}</td>
                    <td class="px-4 py-3">
                        {{ \Illuminate\Support\Carbon::parse($b->date)->translatedFormat('d F Y') }}<br>
                        <span class="text-sm text-gray-500">
                            {{ \Illuminate\Support\Carbon::parse($b->start_time)->format('H:i') }}
                            —
                            {{ \Illuminate\Support\Carbon::parse($b->end_time)->format('H:i') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-700">
                        {{ $b->created_at->translatedFormat('d F Y, H:i') }}
                    </td>
                    <td class="px-4 py-3 text-gray-700">
                        {{ optional($b->event)->name ?? 'Paket Acara' }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="{{ $statusClass($b->status) }}">{{ ucfirst($b->status) }}</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('booking.invoice', $b) }}"
                            class="inline-flex items-center px-3 py-1.5 rounded-lg bg-primary text-white text-sm hover:bg-[#5a0c0f] transition font-semibold">
                            Lihat Invoice
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">Belum ada pesanan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@endsection
