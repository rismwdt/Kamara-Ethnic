<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Pesanan:') }} {{ $booking->booking_code }} - {{ $booking->event_type }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-4">
            <a href="{{ route('pesanan.index') }}">
                <x-secondary-button>
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </x-secondary-button>
            </a>
        </div>

        <div class="bg-white shadow rounded-lg p-6 text-gray-800">
            <table class="table-auto w-full text-sm text-left text-gray-700">
                <tbody class="divide-y divide-gray-200">

                    <tr>
                        <th class="py-2 pr-4 font-medium w-1/3">Kode Pesanan</th>
                        <td>: {{ $booking->booking_code }}</td>
                    </tr>

                    <tr>
                        <th class="py-2 pr-4 font-medium">Paket Acara</th>
                        <td>: {{ optional($booking->event)->name ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th class="py-2 pr-4 font-medium">Harga Paket</th>
                        <td>: Rp{{ number_format((int) $booking->price, 0, ',', '.') }}</td>
                    </tr>

                    <tr>
                        <th class="py-2 pr-4 font-medium">DP</th>
                        <td>: Rp{{ number_format((int) $booking->dp, 0, ',', '.') }}</td>
                    </tr>

                    <tr>
                        <th class="py-2 pr-4 font-medium">Nama Klien</th>
                        <td>: {{ $booking->client_name ?? '-' }}</td>
                    </tr>

                    @if($booking->event_type === 'pernikahan')
                        <tr>
                            <th class="py-2 pr-4 font-medium">Orang Tua Pria</th>
                            <td>: {{ $booking->male_parents ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 pr-4 font-medium">Orang Tua Wanita</th>
                            <td>: {{ $booking->female_parents ?? '-' }}</td>
                        </tr>
                    @else
                        <tr>
                            <th class="py-2 pr-4 font-medium">Nama Acara</th>
                            <td>: {{ $booking->event_name ?? '-' }}</td>
                        </tr>
                    @endif

                    <tr>
                        <th class="py-2 pr-4 font-medium">No. HP</th>
                        <td>: {{ $booking->phone }}</td>
                    </tr>

                    <tr>
                        <th class="py-2 pr-4 font-medium">Email</th>
                        <td>: {{ $booking->email ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th class="py-2 pr-4 font-medium">Nuansa Acara</th>
                        <td>: {{ $booking->nuance ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th class="py-2 pr-4 font-medium">Tanggal</th>
                        <td>: {{ optional($booking->date)->format('d-m-Y') ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th class="py-2 pr-4 font-medium">Waktu</th>
                        <td>:
                            {{ $booking->start_time ? \Illuminate\Support\Str::of($booking->start_time)->substr(0,5) : '-' }}
                            -
                            {{ $booking->end_time ? \Illuminate\Support\Str::of($booking->end_time)->substr(0,5) : '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th class="py-2 pr-4 font-medium">Durasi</th>
                        <td>:
                            @php $m = $booking->durationMinutes(); @endphp
                            {{ $m !== null ? floor($m/60).' jam '.($m%60).' menit' : '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th class="py-2 pr-4 font-medium">Alamat Lengkap</th>
                        <td>: {{ $booking->location_detail }}</td>
                    </tr>

                    @if(!is_null($booking->latitude) && !is_null($booking->longitude))
                        <tr>
                            <th class="py-2 pr-4 font-medium">Koordinat</th>
                            <td>:
                                {{ $booking->latitude }}, {{ $booking->longitude }}
                                — <a class="text-primary underline"
                                     href="https://www.google.com/maps?q={{ $booking->latitude }},{{ $booking->longitude }}"
                                     target="_blank">Lihat di Maps</a>
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <th class="py-2 pr-4 font-medium">Catatan</th>
                        <td>: {{ $booking->notes ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th class="py-2 pr-4 font-medium">Status</th>
                        <td>:
                            @php
                                $statusColor = match($booking->status) {
                                    'tertunda' => 'bg-yellow-100 text-yellow-800',
                                    'diterima' => 'bg-green-100 text-green-800',
                                    'ditolak'  => 'bg-red-100 text-red-800',
                                    'selesai'  => 'bg-indigo-100 text-indigo-800',
                                    default    => 'bg-gray-100 text-gray-800',
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded {{ $statusColor }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th class="py-2 pr-4 font-medium">Prioritas</th>
                        <td>:
                            @php
                                $p = $booking->priority ?? 'normal';
                                $label = $p === 'darurat' ? 'Darurat' : 'Normal';
                                $color = $p === 'darurat' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 py-0.5 rounded text-xs {{ $color }}">{{ $label }}</span>
                            @if($booking->is_family)
                                <span class="ml-2 px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-800">Dari keluarga/relasi</span>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th class="py-2 pr-4 font-medium">Pengisi Acara</th>
                        <td>:
                            @if ($booking->performers->count())
                                <ul class="list-disc list-inside space-y-1 mt-1">
                                    @foreach ($booking->performers as $performer)
                                        <li>
                                            {{ $performer->name }}
                                            @if($performer->pivot?->is_external)
                                                <span class="ml-2 text-xs px-2 py-0.5 rounded bg-purple-100 text-purple-800">Eksternal</span>
                                            @endif
                                            @if($performer->pivot?->confirmation_status)
                                                <span class="ml-2 text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-700">
                                                    {{ $performer->pivot->confirmation_status }}
                                                </span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-gray-500">Tidak ada</span>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th class="py-2 pr-4 font-medium">Foto Lokasi</th>
                        <td>:
                            @if($booking->location_photo)
                                <a href="{{ asset('storage/' . $booking->location_photo) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $booking->location_photo) }}" class="w-48 rounded shadow mt-2" alt="Foto Lokasi">
                                </a>
                            @else
                                <span class="text-gray-500">Tidak ada gambar</span>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th class="py-2 pr-4 font-medium">Bukti TF</th>
                        <td>:
                            @if($booking->image)
                                <a href="{{ asset('storage/' . $booking->image) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $booking->image) }}" class="w-48 rounded shadow mt-2" alt="Bukti Transfer">
                                </a>
                            @else
                                <span class="text-gray-500">Tidak ada gambar</span>
                            @endif
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
