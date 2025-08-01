<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Pesanan') }}
        </h2>
    </x-slot>

    <main class="flex-1 mb-auto bg-white min-h-screen p-6 text-gray-900 flex flex-col">
        <div class="flex justify-between items-center mb-4">
            <a href="{{ route('pesanan.index') }}">
                <x-secondary-button>
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </x-secondary-button>
            </a>
        </div>
        @if (session('warning'))
        @php
        $warnings = collect(session('warning'));
        $performersNoPrevEvent = $warnings->where('reason', 'tidak_ada_acara_sebelumnya')
        ->pluck('performer')->unique();
        $locationWarnings = $warnings
        ->filter(fn($item) => in_array($item['reason'], ['lokasi_belum_terdaftar', 'estimasi_belum_ada']) &&
        is_null($item['performer']))
        ->mapToGroups(fn($item) => [$item['to'] => $item]);
        @endphp
        @if ($performersNoPrevEvent->isNotEmpty())
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4">
            <strong>Info:</strong><br>
            Performer berikut belum punya acara sebelumnya hari ini:
            <ul class="list-disc pl-5 mt-1">
                @foreach ($performersNoPrevEvent as $performer)
                <li>{{ $performer }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @foreach ($locationWarnings as $location => $warnings)
        @php
        $hasMissingLocation = $warnings->contains('reason', 'lokasi_belum_terdaftar');
        $hasMissingEstimate = $warnings->contains('reason', 'estimasi_belum_ada');
        @endphp
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
            <strong>Perhatian:</strong><br>
            @if ($hasMissingLocation && $hasMissingEstimate)
            Alamat <strong>{{ $location }}</strong> belum tersedia <em>dan</em> estimasi belum ada.<br>
            @elseif ($hasMissingLocation)
            Alamat <strong>{{ $location }}</strong> belum tersedia di database.<br>
            @elseif ($hasMissingEstimate)
            Estimasi menuju lokasi <strong>{{ $location }}</strong> belum tersedia.<br>
            @endif
            Silakan kelola di menu <a href="{{ route('lokasi-acara.index') }}"
                class="underline hover:text-blue-900 font-bold">Lokasi</a>.
        </div>
        @endforeach
        @php
        $otherWarnings = $warnings->reject(fn($w) =>
        in_array($w['reason'], ['lokasi_belum_terdaftar', 'estimasi_belum_ada']) && is_null($w['performer'])
        )->reject(fn($w) => $w['reason'] === 'tidak_ada_acara_sebelumnya');
        @endphp
        @if ($otherWarnings->isNotEmpty())
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
            <p class="font-bold mb-1">Peringatan lainnya:</p>
            <ul class="list-disc ml-5 space-y-1">
                @foreach ($otherWarnings as $warning)
                <li>
                    Performer <strong>{{ $warning['performer'] ?? '-' }}</strong>:
                    @switch($warning['reason'])
                    @case('waktu_tidak_cukup')
                    waktu tempuh dari acara sebelumnya tidak cukup.
                    @break
                    @case('lokasi_asal_belum_terdaftar')
                    lokasi asal acara sebelumnya belum terdaftar.
                    @break
                    @default
                    {{ $warning['reason'] }}
                    @endswitch
                </li>
                @endforeach
            </ul>
        </div>
        @endif
        @endif
        <form method="POST" action="{{ route('pesanan.update', $booking->id) }}">
            @csrf
            @method('PUT')
            <div class="bg-white shadow rounded-lg p-6 text-gray-800 mb-6">
                <h3 class="text-lg font-semibold mb-4">Informasi Pesanan</h3>
                <table class="table-auto w-full text-sm text-left text-gray-700">
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <th class="py-2 pr-4 font-medium">Paket Acara</th>
                            <td>: {{ $booking->event->name }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 pr-4 font-medium">Nama Klien</th>
                            <td>: {{ $booking->client_name }}</td>
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
                            <th class="py-2 pr-4 font-medium text-left">Status</th>
                            @php
                            $statusColor = match($booking->status) {
                            'tertunda' => 'bg-yellow-100 text-yellow-800',
                            'diterima' => 'bg-green-100 text-green-800',
                            'ditolak' => 'bg-red-100 text-red-800',
                            'selesai' => 'bg-indigo-100 text-indigo-800',
                            default => 'bg-gray-100 text-gray-800',
                            };
                            @endphp
                            <td>:
                                <span class="text-xs font-semibold px-2 py-1 rounded {{ $statusColor }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="py-2 pr-4 font-medium">Alamat Lengkap</th>
                            <td>: {{ $booking->location_detail }}</td>
                        </tr>
                        {{-- <tr>
                            <td colspan="2"> --}}
                        {{-- Group performer yang tidak punya acara sebelumnya --}}
                        {{-- @php
                                $performersNoPrevEvent = collect($missingEstimates)
                                ->where('reason', 'tidak_ada_acara_sebelumnya')
                                ->pluck('performer')
                                ->unique();
                                @endphp
                                @if ($performersNoPrevEvent->isNotEmpty())
                                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4">
                                    <strong>Info:</strong><br>
                                    Tidak bisa mengecek estimasi karena performer berikut belum punya acara sebelumnya
                                    di hari ini:
                                    <ul class="list-disc pl-5 mt-1">
                                        @foreach ($performersNoPrevEvent as $performer)
                                        <li>{{ $performer }}</li>
                        @endforeach
                        </ul>
            </div>
            @endif --}}
            {{-- Kelola estimasi yang berkaitan dengan lokasi --}}
            {{-- @php
                                $locationWarnings = collect($missingEstimates)
                                ->filter(function ($item) {
                                return in_array($item['reason'], ['lokasi_belum_terdaftar', 'estimasi_belum_ada']) &&
                                $item['performer'] === null;
                                })
                                ->mapToGroups(function ($item) {
                                return [$item['to'] => $item];
                                });
                                @endphp
                                @foreach ($locationWarnings as $location => $warnings)
                                @php
                                $hasMissingLocation = $warnings->contains('reason', 'lokasi_belum_terdaftar');
                                $hasMissingEstimate = $warnings->contains('reason', 'estimasi_belum_ada');
                                @endphp
                                @if ($hasMissingLocation && $hasMissingEstimate)
                                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
                                    <strong>Perhatian:</strong><br>
                                    Alamat <strong>{{ $location }}</strong> belum tersedia di database dan estimasi
            menuju lokasi ini belum tersedia.<br>
            Silakan tambahkan alamat dan estimasi terlebih dahulu di menu <a href="{{ route('lokasi-acara.index') }}"
                class="underline hover:text-blue-900 font-bold">Lokasi</a>.
            </div> --}}
            {{-- @include('admin.lokasi-acara.modal-tambah-lokasi') --}}
            {{-- @elseif ($hasMissingLocation)
                                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
                                    <strong>Perhatian:</strong><br>
                                    Alamat <strong>{{ $location }}</strong> belum tersedia di database.<br>
            Silakan tambahkan alamat terlebih dahulu di menu <a href="{{ route('lokasi-acara.index') }}"
                class="underline hover:text-blue-900 font-bold">Lokasi</a>. --}}
            {{-- <div class="mt-2">
                                        <button
                                            onclick="document.getElementById('modalTambahLokasi').classList.remove('hidden')"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-300 active:bg-blue-800 transition">
                                            <i class="fas fa-plus mr-2"></i> Tambah Lokasi
                                        </button>
                                    </div> --}}
            {{-- </div> --}}
            {{-- @include('admin.lokasi-acara.modal-tambah-lokasi') --}}
            {{-- @elseif ($hasMissingEstimate)
                                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
                                    <strong>Perhatian:</strong><br>
                                    Estimasi menuju lokasi <strong>{{ $location }}</strong> belum tersedia.<br>
            Silakan tambahkan estimasi terlebih dahulu di menu <strong>Lokasi</strong>.
            </div>
            @endif
            @endforeach
            </td>
            </tr> --}}
            {{-- <div class="mb-4">
                            <label for="status">Status Saat Ini</label>
                            <input type="text" readonly class="form-input bg-gray-100"
                                value="{{ ucfirst($booking->status) }}">
            </div> --}}
            </tbody>
            </table>
            </div>
            {{-- Pengisi Acara --}}
            @php
            $rekomendasi = collect($rekomendasi)->flatten(1);
            $rekomendasi_ids = $rekomendasi->pluck('id')->toArray();
            @endphp
            <div class="flex flex-col lg:flex-row-reverse gap-6">
                @if($rekomendasi->isNotEmpty())
                <div class="lg:w-1/3 bg-green-100 border-l-4 border-green-500 text-green-800 p-4 rounded h-fit">
                    <strong>Rekomendasi Performer:</strong>
                    <ul class="list-disc ml-5 mt-1">
                        @foreach ($rekomendasi as $p)
                        <li>{{ $p->name }} <span class="text-sm text-gray-500">({{ $p->category }})</span></li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <div class="lg:w-2/3 bg-white shadow rounded-lg p-6 text-gray-800">
                    @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <strong>Terjadi kesalahan:</strong>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <h3 class="text-lg font-semibold mb-4">Pengisi Acara</h3>
                    <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-6 sm:space-y-0 overflow-x-auto pb-2">
                        @foreach ($categories as $categoryName => $performersInCategory)
                        <div class="sm:w-[160px] w-full">
                            <div class="font-semibold mb-2">
                                {{ ucfirst($categoryName) }}
                                <span class="text-sm text-gray-400">({{ count($performersInCategory) }})</span>
                            </div>
                            <div class="space-y-2">
                                @forelse ($performersInCategory as $performer)
                                @php
                                $isChecked = $booking->performers->contains($performer->id) || in_array($performer->id,
                                $rekomendasi_ids);
                                @endphp
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="performer_ids[]" value="{{ $performer->id }}"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                        {{ $isChecked ? 'checked' : '' }}>
                                    <span>{{ $performer->name }}</span>
                                </label>
                                @empty
                                <p class="text-sm text-gray-500 italic">Belum ada performer.</p>
                                @endforelse
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <x-primary-button class="mt-6">Simpan</x-primary-button>
    </main>
</x-app-layout>
