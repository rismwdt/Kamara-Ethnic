<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pesanan') }}
        </h2>
        {{-- CSRF token (boleh di <head> layout juga) --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </x-slot>

    <main class="flex-1 mb-auto bg-white min-h-screen p-6 text-gray-900 flex flex-col">
        @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded">
            {{ session('success') }}
        </div>
        @endif

        <div class="flex justify-between items-center mb-4">
            <div class="flex space-x-2">
                <x-primary-button x-data x-on:click="$dispatch('open-modal', 'modal-unduh-laporan')">
                    <i class="fas fa-download mr-1"></i> Unduh Laporan
                </x-primary-button>

                {{-- Tombol cek semua performer --}}
                <x-primary-button class="bg-green-600 hover:bg-green-700" onclick="cekSemuaPerformer()">
                    <i class="fas fa-magic mr-1"></i> Cek Semua Performer
                </x-primary-button>
            </div>

            <x-modal name="modal-unduh-laporan" focusable>
                <div class="relative p-6">
                    <button type="button" class="absolute top-4 right-4 text-gray-500 hover:text-red-600 text-3xl"
                        x-on:click="$dispatch('close')">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <form method="GET" action="{{ route('admin.pesanan.cetak') }}" target="_blank">
                        <h2 class="text-lg font-medium text-gray-900">Unduh Laporan Pemesanan</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Pilih rentang tanggal untuk mengunduh laporan pemesanan acara.
                        </p>

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
                            <x-primary-button type="submit" class="ml-3">Unduh PDF</x-primary-button>
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
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </x-slot>

            @foreach ($bookings as $index => $booking)
            <tr id="booking-row-{{ $booking->id }}"
                data-booking="{{ $booking->id }}"
                data-event="{{ $booking->event_id }}"
                data-date="{{ $booking->date }}"
                data-start="{{ $booking->start_time }}"
                data-end="{{ $booking->end_time }}"
                data-location="{{ $booking->location_detail }}"
                data-lat="{{ $booking->latitude }}"
                data-lng="{{ $booking->longitude }}"
            >
                <td class="px-4 py-2">{{ $bookings->firstItem() + $index }}</td>
                <td class="px-4 py-2">{{ $booking->client_name }}</td>
                <td class="px-4 py-2">{{ $booking->event->name }}</td>
                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($booking->date)->format('d-m-Y') }}</td>
                <td class="px-4 py-2">
                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}
                    -
                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                </td>
                <td class="px-4 py-2">{{ $booking->location_detail }}</td>
                <td class="-px-4 py-2 max-w-md">
                    @if ($booking->performers->count())
                        <ul class="list-disc list-inside space-y-1 max-h-24 overflow-y-auto pr-1 text-sm">
                            @foreach ($booking->performers as $performer)
                                <li class="break-words">{{ $performer->name }}</li>
                            @endforeach
                        </ul>
                    @else
                        <x-primary-button
                            class="text-xs px-2 py-1"
                            :id="'btn-cek-'.$booking->id"
                            onclick="cekPerformer({{ $booking->id }}, true)">
                            <span class="inline-flex items-center">
                                <i class="fas fa-magic mr-1"></i>
                                <span>Cek & Assign Performer</span>
                                <span class="ml-2 hidden" id="spinner-{{ $booking->id }}">⏳</span>
                            </span>
                        </x-primary-button>
                        <span id="status-{{ $booking->id }}" class="ml-2 text-sm" aria-live="polite"></span>
                    @endif
                </td>
                <td class="px-4 py-2">
                    @php
                        $statusColor = match($booking->status) {
                            'tertunda' => 'bg-yellow-100 text-yellow-800',
                            'diterima' => 'bg-green-100 text-green-800',
                            'ditolak' => 'bg-red-100 text-red-800',
                            'selesai' => 'bg-indigo-100 text-indigo-800',
                            default => 'bg-gray-100 text-gray-800',
                        };
                    @endphp
                    <span class="px-2 py-1 text-xs font-semibold rounded {{ $statusColor }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </td>
                <td class="px-4 py-2">
                    <div class="flex justify-center items-center space-x-2">
                        <a href="{{ route('admin.pesanan.show', $booking->id) }}">
                            <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                                <i class="fas fa-eye ml-1"></i>
                            </x-primary-button>
                        </a>
                        <a href="{{ route('pesanan.edit', $booking->id) }}">
                            <x-primary-button class="text-xs px-2 py-1">
                                <i class="fas fa-edit"></i>
                            </x-primary-button>
                        </a>
                        <x-danger-button type="button"
                            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'delete-{{ $booking->id }}' }))">
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

    {{-- Endpoint JS agar tidak hard-code URL --}}
    <script>
      window.ENDPOINTS = {
        cekJadwal: @json(route('pesanan.cek-jadwal'))
      };
    </script>

    {{-- Axios + CSRF setup --}}
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
        statusEl.textContent = '⏳ Mengecek performer...';
        statusEl.classList.remove('text-red-600','text-green-600');

        const payload = {
          booking_id: row.dataset.booking,
          event_id:   row.dataset.event,
          date:       row.dataset.date,
          start_time: row.dataset.start,
          end_time:   row.dataset.end,
          location:   row.dataset.location,
          latitude:   parseFloat(row.dataset.lat),
          longitude:  parseFloat(row.dataset.lng),
          assign:     !!assign
        };

        try {
          const res = await axios.post(window.ENDPOINTS.cekJadwal, payload);
          if (res.data.available) {
            const name = res.data.performer_name ?? '(tanpa nama)';
            statusEl.textContent = `✅ Performer tersedia${res.data.assigned ? ' dan sudah di-assign' : ''}: ${name}`;
            statusEl.classList.add('text-green-600');

            // Optional: update badge status jadi "Diterima" jika assigned
            if (res.data.assigned) {
              const badge = row.querySelector('td:nth-child(8) span');
              if (badge) {
                badge.textContent = 'Diterima';
                badge.className = 'px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800';
              }
            }
          } else {
            statusEl.textContent = "❌ Performer tidak tersedia: " + (res.data.reason ?? 'Tidak diketahui');
            statusEl.classList.add('text-red-600');
          }
        } catch (err) {
          console.error(err);
          if (err.response?.status === 422) {
            const msg = Object.values(err.response.data.errors ?? {}).flat().join('; ');
            statusEl.textContent = "⚠ Validasi gagal: " + (msg || 'Data tidak valid');
          } else if (err.response?.status === 419) {
            statusEl.textContent = "⚠ Sesi kedaluwarsa (CSRF). Muat ulang halaman.";
          } else {
            statusEl.textContent = "⚠ Terjadi kesalahan!";
          }
          statusEl.classList.add('text-red-600');
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
