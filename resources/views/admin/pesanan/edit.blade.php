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
                            <th class="py-2 pr-4 font-medium">Alamat Lengkap</th>
                            <td>: {{ $booking->location_detail }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                {{-- Group performer yang tidak punya acara sebelumnya --}}
                                @php
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
                                @endif
                                {{-- Kelola estimasi yang berkaitan dengan lokasi --}}
                                @php
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
                                    Silakan tambahkan alamat dan estimasi terlebih dahulu di menu
                                    <strong>Lokasi</strong>.
                                </div>
                                @include('admin.lokasi-acara.modal-tambah-lokasi')
                                @elseif ($hasMissingLocation)
                                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
                                    <strong>Perhatian:</strong><br>
                                    Alamat <strong>{{ $location }}</strong> belum tersedia di database.<br>
                                    Silakan tambahkan alamat terlebih dahulu di menu <strong>Lokasi</strong>.
                                    {{-- <div class="mt-2">
                                        <button
                                            onclick="document.getElementById('modalTambahLokasi').classList.remove('hidden')"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-300 active:bg-blue-800 transition">
                                            <i class="fas fa-plus mr-2"></i> Tambah Lokasi
                                        </button>
                                    </div> --}}
                                </div>
                                @include('admin.lokasi-acara.modal-tambah-lokasi')
                                @elseif ($hasMissingEstimate)
                                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
                                    <strong>Perhatian:</strong><br>
                                    Estimasi menuju lokasi <strong>{{ $location }}</strong> belum tersedia.<br>
                                    Silakan tambahkan estimasi terlebih dahulu di menu <strong>Lokasi</strong>.
                                </div>
                                @endif
                                @endforeach
                            </td>
                        </tr>
                        <div class="mb-4">
                            <label for="status">Status Saat Ini</label>
                            <input type="text" readonly class="form-input bg-gray-100"
                                value="{{ ucfirst($booking->status) }}">
                        </div>
                    </tbody>
                </table>
            </div>
            {{-- Pengisi Acara --}}
            <div class="bg-white shadow rounded-lg p-6 text-gray-800 mt-6">
                <h3 class="text-lg font-semibold mb-4">Pengisi Acara</h3>
                <div class="flex space-x-6 overflow-x-auto pb-2">
                    @foreach ($categories as $categoryName => $performersInCategory)
                    <div class="min-w-[200px]">
                        <div class="font-semibold mb-2">{{ $categoryName }}</div>
                        <div class="space-y-2">
                            @forelse ($performersInCategory as $performer)
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="performer_ids[]" value="{{ $performer->id }}"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    {{ $booking->performers->contains($performer->id) ? 'checked' : '' }}>
                                <span>{{ $performer->name }}</span>
                            </label>
                            @empty
                            <p class="text-sm text-gray-500 italic">Belum ada</p>
                            @endforelse
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <x-primary-button class="mt-6">Simpan</x-primary-button>
        </form>
    </main>
</x-app-layout>
