@php use Illuminate\Support\Str; @endphp

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
            <div class="flex justify-between w-full">
                <x-add-button href="{{ route('pesanan.create') }}" label="Tambah Pesanan" />

                <x-primary-button x-data x-on:click="$dispatch('open-modal', 'modal-unduh-laporan')">
                    <i class="fas fa-download mr-1"></i>Laporan
                </x-primary-button>
            </div>

            <x-modal name="modal-unduh-laporan" focusable>
                <div class="relative p-6">
                    <button type="button" class="absolute top-4 right-4 text-gray-500 hover:text-red-600"
                        x-on:click="$dispatch('close')" aria-label="Tutup">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
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
                    <th class="px-4 py-2">Tanggal & Waktu</th>
                    <th class="px-4 py-2">Alamat Lengkap</th>
                    <th class="px-4 py-2">Pengisi Acara</th>
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
            'ditolak' => 'bg-red-100 text-red-800',
            'selesai' => 'bg-indigo-100 text-indigo-800',
            default => 'bg-gray-100 text-gray-800',
            };
            @endphp

            <tr id="booking-row-{{ $booking->id }}">
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

                <td class="px-4 py-2 text-sm">
                    <div>{{ \Carbon\Carbon::parse($booking->date)->format('d-m-Y') }}</div>
                    <div class="text-gray-600 mt-1">
                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                        {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                    </div>
                </td>

                <td class="px-4 py-2 max-w-xs">
                    <div class="max-h-16 overflow-y-auto pr-1 text-sm break-words">
                        {{ $booking->location_detail }}
                    </div>
                </td>

                <td class="px-4 py-2 max-w-md">
                    @if ($booking->performers->count())
                    <ul class="list-disc list-outside pl-5 max-h-24 overflow-y-auto text-sm">
                        @foreach ($booking->performers as $performer)
                        <li class="break-words">
                            {{ $performer->name }}
                            @if($performer->pivot?->is_external)
                            <span
                                class="ml-1 text-[10px] px-1.5 py-0.5 rounded bg-purple-100 text-purple-800">Eksternal</span>
                            @endif
                        </li>
                        @endforeach
                    </ul>

                    @else
                    <x-primary-button class="text-xs px-2 py-1 bg-amber-600 hover:bg-amber-700"
                        :id="'btn-manual-'.$booking->id" x-data
                        x-on:click="$dispatch('open-modal', 'modal-manual-{{ $booking->id }}')">
                        <span class="inline-flex items-center">
                            <i class="fas fa-user-check mr-1"></i>
                            <span>Tambah</span>
                        </span>
                    </x-primary-button>
                    @endif
                </td>

                <td class="px-4 py-2">
                    <span id="status-badge-{{ $booking->id }}"
                        class="px-2 py-1 text-xs font-semibold rounded {{ $statusColor }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </td>

                <td class="px-4 py-2">
                    <div class="flex justify-center items-center space-x-2">
                        <a href="{{ route('admin.pesanan.show', $booking->id) }}">
                            <x-primary-button class="bg-indigo-600 hover:bg-indigo-700" title="Lihat">
                                <i class="fas fa-eye"></i>
                            </x-primary-button>
                        </a>
                        {{-- <a href="{{ route('pesanan.edit', $booking->id) }}">
                        <x-primary-button class="text-xs px-2 py-1" title="Edit">
                            <i class="fas fa-edit"></i>
                        </x-primary-button>
                        </a> --}}
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
            simpanPerformer: @json(route('pesanan.tambah-pengisi-acara.store')),
        };
    </script>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] =
            document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function toggleRole(bookingId, roleSlug, checked) {
            const scope = document.getElementById(`role-${roleSlug}-${bookingId}`);
            if (!scope) return;
            scope.querySelectorAll('.kandidat-check').forEach(cb => cb.checked = !!checked);
        }

        async function simpanPenetapanManual(bookingId) {
            const row = document.querySelector(`#booking-row-${bookingId}`);
            const checks = document.querySelectorAll(`#kandidat-wrap-${bookingId} .kandidat-check:checked`);
            const note = document.getElementById(`manual-note-${bookingId}`) ? .value || '';
            const statusEl = document.getElementById(`status-${bookingId}`);

            if (!checks.length) {
                if (statusEl) {
                    statusEl.textContent = '⚠ Pilih minimal satu pengisi acara.';
                    statusEl.classList.add('text-red-600');
                }
                return;
            }

            const performer_ids = Array.from(checks).map(c => Number(c.value));

            try {
                if (statusEl) {
                    statusEl.textContent = '⏳ Menyimpan penetapan…';
                    statusEl.classList.remove('text-red-600', 'text-green-600');
                }

                const {
                    data
                } = await axios.post(window.ENDPOINTS.simpanPerformer, {
                    booking_id: row.dataset.booking,
                    performer_ids,
                    note
                });

                if (data ? .success) {
                    const namesArray = Array.isArray(data.assigned) ?
                        data.assigned.map(p => p.name) :
                        (data.names || []);

                    const performerCell = row.querySelector('td:nth-child(7)');
                    if (performerCell && namesArray.length) {
                        const listHtml = namesArray.map(n => `<li class="break-words">${n}</li>`).join('');
                        performerCell.innerHTML =
                            `<ul class="list-disc list-inside space-y-1 max-h-24 overflow-y-auto pr-1 text-sm">${listHtml}</ul>`;
                    }

                    const badge = document.getElementById(`status-badge-${bookingId}`);
                    const s = (data.booking_status || 'diterima').toLowerCase();
                    const map = {
                        tertunda: 'px-2 py-1 text-xs font-semibold rounded bg-yellow-100 text-yellow-800',
                        diterima: 'px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800',
                        ditolak: 'px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800',
                        selesai: 'px-2 py-1 text-xs font-semibold rounded bg-indigo-100 text-indigo-800'
                    };
                    if (badge) {
                        badge.textContent = s.charAt(0).toUpperCase() + s.slice(1);
                        badge.className = map[s] ||
                            'px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800';
                    }

                    if (statusEl) {
                        statusEl.textContent = '✅ Penetapan disimpan.';
                        statusEl.classList.add('text-green-600');
                    }
                    window.dispatchEvent(new CustomEvent('close-modal', {
                        detail: `modal-manual-${bookingId}`
                    }));
                } else {
                    throw new Error('Gagal menyimpan');
                }

            } catch (e) {
                if (statusEl) {
                    statusEl.textContent = '⚠ Gagal menyimpan penetapan.';
                    statusEl.classList.add('text-red-600');
                }
            }
        }
    </script>
</x-app-layout>
