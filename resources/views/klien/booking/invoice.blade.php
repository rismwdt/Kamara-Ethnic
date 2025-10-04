@extends('layouts.client')

@section('title','Invoice Pesanan')

@section('content')
@php
use Illuminate\Support\Carbon;
function rupiah($n){ return 'Rp '.number_format((int)$n, 0, ',', '.'); }
$eventName = optional($booking->event)->name ?? 'Paket Acara';
$dateLabel = Carbon::parse($booking->date)->translatedFormat('d F Y');
@endphp

<style>
    @page {
        size: A4;
        margin: 10mm;
    }

    @media print {
        nav,
        header,
        footer,
        .site-header,
        .site-footer,
        .app-navbar,
        .app-footer {
            display: none !important;
        }

        body * {
            visibility: hidden;
        }

        #invoicePaper,
        #invoicePaper * {
            visibility: visible;
        }

        #invoicePaper {
            position: absolute;
            left: 0;
            top: 0;
            width: 190mm;
            min-height: 277mm;
            transform: scale(0.98);
            transform-origin: top left;
        }

        .avoid-break {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        table {
            page-break-inside: auto;
        }

        tr,
        td,
        th {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .print-p-4 {
            padding: 12px !important;
        }

        .print-p-6 {
            padding: 16px !important;
        }

        .print-text-sm {
            font-size: 12px !important;
            line-height: 1.3 !important;
        }

        .print-text-xs {
            font-size: 11px !important;
            line-height: 1.25 !important;
        }

        .print-hidden {
            display: none !important;
        }
    }
</style>

<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div id="invoicePaper" class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl overflow-hidden">

            <div
                class="p-6 print-p-6 border-b border-gray-200 dark:border-gray-700 flex items-start justify-between avoid-break">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kamara Ethnic</h1>
                    <p class="text-sm print-text-sm text-gray-500 dark:text-gray-400 mt-1">Pertunjukan Seni Tradisional
                        Sunda</p>
                    <p class="text-sm print-text-sm text-gray-500 dark:text-gray-400">Email: info@kamara-ethnic.id</p>
                </div>
                <div class="text-right">
                    <div class="text-xs print-text-xs uppercase text-gray-500 dark:text-gray-400">Kode Booking</div>
                    <div class="text-base font-semibold text-gray-900 dark:text-white">{{ $booking->booking_code }}
                    </div>

                    <div class="mt-3 text-xs print-text-xs uppercase text-gray-500 dark:text-gray-400">Tanggal Invoice
                    </div>
                    <div class="text-sm print-text-sm text-gray-900 dark:text-white">{{ now()->format('d/m/Y H:i') }}
                    </div>

                    <span class="inline-flex items-center mt-3 px-2 py-0.5 rounded-full text-xs font-medium avoid-break
            @class([
              'bg-yellow-100 text-yellow-800' => $booking->status === 'tertunda',
              'bg-green-100 text-green-800'   => in_array($booking->status, ['disetujui','selesai']),
              'bg-red-100 text-red-800'       => $booking->status === 'ditolak',
              'bg-gray-100 text-gray-800'     => !in_array($booking->status, ['tertunda','disetujui','selesai','ditolak']),
            ])">
                        Status: {{ ucfirst($booking->status) }}
                    </span>
                </div>
            </div>

            <div class="p-6 print-p-6 grid grid-cols-1 md:grid-cols-2 gap-6 avoid-break">
                <div>
                    <h3 class="text-sm print-text-sm font-semibold text-gray-700 dark:text-gray-300">Ditagihkan Kepada
                    </h3>
                    <div class="mt-2 text-sm print-text-sm text-gray-800 dark:text-gray-100">
                        <div class="font-medium">{{ $booking->client_name }}</div>
                        @if($booking->email)<div>Email: {{ $booking->email }}</div>@endif
                        <div>Telepon: {{ $booking->phone }}</div>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm print-text-sm font-semibold text-gray-700 dark:text-gray-300">Detail Acara</h3>
                    <div class="mt-2 text-sm print-text-sm text-gray-800 dark:text-gray-100 space-y-1">
                        <div>Jenis: <span
                                class="font-medium capitalize">{{ str_replace('_',' ', $booking->event_type) }}</span>
                        </div>
                        <div>Paket: <span class="font-medium">{{ $eventName }}</span></div>
                        <div>Tanggal: <span class="font-medium">{{ $dateLabel }}</span></div>
                        <div>Waktu: <span class="font-medium">{{ $booking->start_time }} -
                                {{ $booking->end_time }}</span></div>
                        @if($booking->nuance)
                        <div>Nuansa/Tema: <span class="font-medium">{{ $booking->nuance }}</span></div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="px-6 pb-6 print-p-6 avoid-break">
                <h3 class="text-sm print-text-sm font-semibold text-gray-700 dark:text-gray-300">Lokasi Acara</h3>
                <p class="text-sm print-text-sm text-gray-800 dark:text-gray-100 whitespace-pre-line">
                    {{ $booking->location_detail }}
                </p>
            </div>

            <div class="p-6 print-p-6 avoid-break">
                <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs print-text-xs font-medium text-gray-500 uppercase">
                                    Item</th>
                                <th
                                    class="px-4 py-3 text-right text-xs print-text-xs font-medium text-gray-500 uppercase w-40">
                                    Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr>
                                <td class="px-4 py-4">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $eventName }}</div>
                                    <div class="text-xs print-text-xs text-gray-500 dark:text-gray-400">
                                        Jasa pertunjukan seni tradisional ({{ $booking->event_type }})
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-right text-gray-900 dark:text-gray-100">{{ rupiah($price) }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-700/30">
                            <tr>
                                <td
                                    class="px-4 py-3 text-right text-sm print-text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Subtotal</td>
                                <td
                                    class="px-4 py-3 text-right text-sm print-text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ rupiah($price) }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-right text-sm print-text-sm text-gray-700 dark:text-gray-300">
                                    DP (50%)</td>
                                <td class="px-4 py-3 text-right text-sm print-text-sm text-gray-900 dark:text-gray-100">
                                    - {{ rupiah($dp) }}</td>
                            </tr>
                            <tr>
                                <td
                                    class="px-4 py-3 text-right text-base print-text-sm font-semibold text-gray-800 dark:text-gray-200">
                                    Sisa Pembayaran</td>
                                <td
                                    class="px-4 py-3 text-right text-base print-text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ rupiah($remain) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($booking->notes || $booking->description)
                <div class="mt-4 avoid-break">
                    <h4 class="text-sm print-text-sm font-semibold text-gray-700 dark:text-gray-300">Catatan</h4>
                    <div class="mt-1 text-sm print-text-sm text-gray-800 dark:text-gray-100 whitespace-pre-line">
                        {{ trim(($booking->description ? "Deskripsi: ".$booking->description."\n" : '').($booking->notes ?? '')) }}
                    </div>
                </div>
                @endif

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 avoid-break">
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 print-p-4">
                        <h4 class="text-sm print-text-sm font-semibold text-gray-700 dark:text-gray-300">Informasi
                            Pembayaran</h4>
                        <div class="mt-2 text-sm print-text-sm text-gray-800 dark:text-gray-100">
                            Bank: <span class="font-medium">BCA</span><br>
                            No. Rekening: <span class="font-medium">7751463093</span><br>
                            a/n <span class="font-medium">Fitri Fitria</span>
                        </div>
                    </div>
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 print-p-4">
                        <h4 class="text-sm print-text-sm font-semibold text-gray-700 dark:text-gray-300">Rangkuman</h4>
                        <div class="mt-2 text-sm print-text-sm text-gray-800 dark:text-gray-100 space-y-1">
                            <div>Harga Paket: <span class="font-medium">{{ rupiah($price) }}</span></div>
                            <div>DP Dibayar: <span class="font-medium">{{ rupiah($dp) }}</span></div>
                            <div>Sisa Pembayaran: <span class="font-medium">{{ rupiah($remain) }}</span></div>
                        </div>
                        <div class="mt-3 text-xs print-text-xs text-gray-600 dark:text-gray-400 space-y-1">
                            <p><strong>*</strong> Pembayaran harus <span class="font-semibold">lunas H-3 sebelum
                                    acara</span>.</p>
                            <p><strong>*</strong> Jika membatalkan acara, <span class="font-semibold">DP yang sudah
                                    masuk akan hangus</span> dan tidak bisa dikembalikan.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="p-6 border-t border-gray-200 dark:border-gray-700 text-sm text-gray-600 dark:text-gray-400 flex justify-between print-hidden">
                <div>Terima kasih telah memesan layanan Kamara Ethnic.</div>
                <button onclick="window.print()"
                    class="inline-flex items-center px-1.5 py-1.5 rounded-lg bg-primary text-white text-sm hover:bg-[#5a0c0f] transition font-semibold">Cetak
                    Invoice</button>
            </div>
        </div>
    </div>
</div>
@endsection
