<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pesanan') }}
        </h2>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </x-slot>

    <main class="flex-1 mb-auto bg-white min-h-screen p-6 text-gray-900 flex flex-col">
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif
        <div class="flex justify-between items-center mb-4">
            <div class="flex justify-between w-full px-4">
    <x-primary-button x-data x-on:click="$dispatch('open-modal', 'modal-unduh-laporan')">
        <i class="fas fa-download mr-1"></i> Unduh Laporan
    </x-primary-button>
    <x-primary-button class="bg-green-600 hover:bg-green-700" onclick="cekSemuaPerformer()">
        <i class="fas fa-magic mr-1"></i> Cek Rekomendasi Pengisi Acara
    </x-primary-button>
</div>
            {{-- Modal Unduh Laporan --}}
            <x-modal name="modal-unduh-laporan" focusable>
                <div class="relative p-6">
                    <button type="button" class="absolute top-4 right-4 text-gray-500 hover:text-red-600"
                            x-on:click="$dispatch('close')" aria-label="Tutup">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    <form method="GET" action="{{ route('admin.pesanan.cetak') }}" target="_blank">
                        <h2 class="text-lg font-medium text-gray-900">Unduh Laporan Pemesanan</h2>
                        <p class="mt-1 text-sm text-gray-600">Pilih rentang tanggal untuk mengunduh laporan.</p>
                        <div class="mt-4">
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Awal</label>
                            <input type="date" name="start_date" id="start_date" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="mt-4">
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                            <input type="date" name="end_date" id="end_date" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="mt-6 flex justify-end">
                            <x-secondary-button x-on:click.prevent="$dispatch('close')">Batal</x-secondary-button>
                            <x-primary-button type="submit" class="ml-3">Lihat Laporan</x-primary-button>
                        </div>
                    </form>
                </div>
            </x-modal>
        </div>
        <x-table>
            <x-slot name="thead">
                <tr>
                    <th class="px-4 py-2">No.</th>
                    <th class="px-4 py-2">Nama Klien</th>
                    <th class="px-4 py-2">Paket</th>
                    <th class="px-4 py-2">Tanggal</th>
                    <th class="px-4 py-2">Waktu</th>
                    <th class="px-4 py-2">Alamat Lengkap</th>
                    <th class="px-4 py-2">Pengisi Acara</th>
                    <th class="px-4 py-2">Prioritas</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </x-slot>
            @foreach ($bookings as $index => $booking)
                @php
                    $priority = $booking->priority ?? 'normal';
                    $priorityLabel = $priority === 'darurat' ? 'Darurat' : 'Normal';
                    $priorityClass = $priority === 'darurat'
                        ? 'bg-red-100 text-red-800'
                        : 'bg-gray-100 text-gray-800';
                    $statusColor = match($booking->status) {
                        'tertunda' => 'bg-yellow-100 text-yellow-800',
                        'diterima' => 'bg-green-100 text-green-800',
                        'ditolak'  => 'bg-red-100 text-red-800',
                        'selesai'  => 'bg-indigo-100 text-indigo-800',
                        default    => 'bg-gray-100 text-gray-800',
                    };
                @endphp
                <tr id="booking-row-{{ $booking->id }}"
    data-booking="{{ $booking->id }}"
    data-event="{{ $booking->event_id }}"
    {{-- pastikan Y-m-d --}}
    data-date="{{ optional($booking->date)->format('Y-m-d') }}"
    {{-- pastikan H:i --}}
    data-start="{{ $booking->start_time ? \Illuminate\Support\Str::of($booking->start_time)->substr(0,5) : '' }}"
    data-end="{{ $booking->end_time ? \Illuminate\Support\Str::of($booking->end_time)->substr(0,5) : '' }}"
    data-location="{{ $booking->location_detail }}"
    data-lat="{{ $booking->latitude }}"
    data-lng="{{ $booking->longitude }}"
>

                    <td class="px-4 py-2">{{ $bookings->firstItem() + $index }}</td>
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            <span>{{ $booking->client_name }}</span>
                            @if($booking->is_family)
                                <span class="px-2 py-0.5 rounded text-[10px] bg-blue-100 text-blue-800">Keluarga</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-2">{{ $booking->event->name }}</td>
                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($booking->date)->format('d-m-Y') }}</td>
                    <td class="px-4 py-2">
                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                        {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                    </td>
                    <td class="px-4 py-2">{{ $booking->location_detail }}</td>
                    <td class="px-4 py-2 max-w-md">
                        @if ($booking->performers->count())
                            <ul class="list-disc list-inside space-y-1 max-h-24 overflow-y-auto pr-1 text-sm">
                                @foreach ($booking->performers as $performer)
                                    <li class="break-words">
                                        {{ $performer->name }}
                                        @if($performer->pivot?->is_external)
                                            <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded bg-purple-100 text-purple-800">Eksternal</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <x-primary-button
                                class="text-xs px-2 py-1"
                                :id="'btn-cek-'.$booking->id"
                                onclick="cekPerformer({{ $booking->id }}, true)">
                                <span class="inline-flex items-center">
                                    <i class="fas fa-magic mr-1"></i>
                                    <span>Cek & Tetapkan</span>
                                    <span class="ml-2 hidden" id="spinner-{{ $booking->id }}">⏳</span>
                                </span>
                            </x-primary-button>
                            <span id="status-{{ $booking->id }}" class="ml-2 text-sm" aria-live="polite"></span>
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 text-xs font-semibold rounded {{ $priorityClass }}">
                            {{ $priorityLabel }}
                        </span>
                    </td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 text-xs font-semibold rounded {{ $statusColor }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex justify-center items-center space-x-2">
                            <a href="{{ route('admin.pesanan.show', $booking->id) }}">
                                <x-primary-button class="bg-indigo-600 hover:bg-indigo-700" title="Lihat">
                                    <i class="fas fa-eye ml-1"></i>
                                </x-primary-button>
                            </a>
                            <a href="{{ route('pesanan.edit', $booking->id) }}">
                                <x-primary-button class="text-xs px-2 py-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </x-primary-button>
                            </a>
                            <x-danger-button type="button"
                                onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'delete-{{ $booking->id }}' }))"
                                title="Hapus">
                                <i class="fas fa-trash"></i>
                            </x-danger-button>
                            <x-modal-delete name="delete-{{ $booking->id }}" :itemId="$booking->id"
                                :itemName="$booking->booking_code" route="pesanan.destroy" />
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-table>
        <div class="mt-8 flex justify-center">
            {{ $bookings->links() }}
        </div>
    </main>

    <script>
        window.ENDPOINTS = {
            cekJadwal: @json(route('pesanan.cek-jadwal'))
        };
    </script>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] =
            document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        function setLoading(bookingId, on) {
            const btn = document.getElementById(`btn-cek-${bookingId}`);
            const spn = document.getElementById(`spinner-${bookingId}`);
            if (btn) btn.disabled = !!on;
            if (spn) spn.classList.toggle('hidden', !on);
        }

        async function cekPerformer(bookingId, assign = false) {
            const statusEl = document.querySelector(`#status-${bookingId}`);
            const row = document.querySelector(`#booking-row-${bookingId}`);

            setLoading(bookingId, true);
            if (statusEl) {
                statusEl.textContent = '⏳ Mengecek performer...';
                statusEl.classList.remove('text-red-600','text-green-600');
            }

            const latRaw = row.dataset.lat;
            const lngRaw = row.dataset.lng;

            const payload = {
                booking_id: row.dataset.booking,
                event_id:   row.dataset.event,
                date:       row.dataset.date,
                start_time: row.dataset.start,
                end_time:   row.dataset.end,
                location:   row.dataset.location || null,
                latitude:   latRaw ? parseFloat(latRaw) : null,
                longitude:  lngRaw ? parseFloat(lngRaw) : null,
                assign:     !!assign
            };

            try {
    const res = await axios.post(window.ENDPOINTS.cekJadwal, payload);
    if (res.data.available) {
        const name = res.data.performer_name ?? '(tanpa nama)';
        if (statusEl) {
            statusEl.textContent = `✅ Performer tersedia${res.data.assigned ? ' dan sudah di-assign' : ''}: ${name}`;
            statusEl.classList.add('text-green-600');
        }

        // === UPDATE UI SETELAH ASSIGN ===
        if (res.data.assigned) {
            // 1) Update badge Status sesuai 'booking_status' dari server
            const statusCell = row.querySelector('td:nth-child(9) span'); // kolom Status = ke-9
            const s = (res.data.booking_status || '').toLowerCase();      // 'diterima' / 'tertunda' / 'ditolak' / 'selesai'
            if (statusCell && s) {
                statusCell.textContent = s.charAt(0).toUpperCase() + s.slice(1);
                const map = {
                    tertunda: 'px-2 py-1 text-xs font-semibold rounded bg-yellow-100 text-yellow-800',
                    diterima: 'px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800',
                    ditolak:  'px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800',
                    selesai:  'px-2 py-1 text-xs font-semibold rounded bg-indigo-100 text-indigo-800'
                };
                statusCell.className = map[s] || 'px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800';
            }

            // 2) Tampilkan nama performer di kolom "Pengisi Acara" (kolom ke-7)
            const performerCell = row.querySelector('td:nth-child(7)');
            if (performerCell) {
                const names = (res.data.performer_name || '').split(',').map(s => s.trim()).filter(Boolean);
                if (names.length) {
                    performerCell.innerHTML =
                        '<ul class="list-disc list-inside space-y-1 max-h-24 overflow-y-auto pr-1 text-sm">'
                        + names.map(n => `<li class="break-words">${n}</li>`).join('')
                        + '</ul>';
                }
            }
        }
    } else {
        // === BLOK BARU: tampilkan alasan detail + GAP ===
        if (statusEl) {
            let msg = "❌ Performer tidak tersedia: " + (res.data.reason ?? 'Tidak diketahui');
            if (res.data.gaps) {
                const list = Object.entries(res.data.gaps)
                  .map(([roleId, g]) => `Role ${roleId}: butuh ${g.need}, tersedia ${g.available}`)
                  .join(' | ');
                msg += " — Gap: " + list;
            }
            statusEl.textContent = msg;
            statusEl.classList.add('text-red-600');
        }
    }
            } catch (err) {
                console.error(err);
                if (statusEl) {
                    if (err.response?.status === 422) {
                        const msg = Object.values(err.response.data.errors ?? {}).flat().join('; ');
                        statusEl.textContent = "⚠ Validasi gagal: " + (msg || 'Data tidak valid');
                    } else if (err.response?.status === 419) {
                        statusEl.textContent = "⚠ Sesi kedaluwarsa (CSRF). Muat ulang halaman.";
                    } else {
                        statusEl.textContent = "⚠ Terjadi kesalahan!";
                    }
                    statusEl.classList.add('text-red-600');
                }
            } finally {
                setLoading(bookingId, false);
            }
        }

        function cekSemuaPerformer() {
            const rows = document.querySelectorAll('tr[id^="booking-row-"]');
            rows.forEach(row => {
                const bookingId = row.id.replace('booking-row-', '');
                const performersEl = row.querySelector('td:nth-child(7) ul');
                if (!performersEl) {
                    cekPerformer(bookingId, true);
                }
            });
        }
    </script>
</x-app-layout>
