<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Pemesanan</title>
    <link rel="icon" href="{{ asset('img/title.png') }}" type="image/x-icon" class="rounded-full">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 6px; border: 1px solid #000; }
        th { background-color: #eee; }
    </style>
</head>
<body>
    <h2>Laporan Pemesanan Acara</h2>
    <p>Periode: {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Pesanan</th>
                <th>Nama Klien</th>
                <th>Tanggal Acara</th>
                <th>Paket Acara</th>
                <th>Lokasi</th>
                <th>Pengisi Acara</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $booking->booking_code }}</td>
                    <td>{{ $booking->client_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($booking->date)->format('d-m-Y') }}
                        <br>{{ $booking->start_time }} - {{ $booking->end_time }}
                    </td>
                    <td>{{ $booking->event->name }} - {{$booking->event->price}}</td>
                    <td>{{ $booking->location_detail }}</td>
                    <td>
                        <ul>
                            @foreach ($booking->performers as $performer)
                            <li class="break-words">{{ $performer->name }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td>{{ ucfirst($booking->status) }}</td>
                </tr>
            @empty
                <tr><td colspan="7">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
