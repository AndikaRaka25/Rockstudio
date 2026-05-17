<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Resi Booking #{{ $booking->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; line-height: 1.5; }
        .header { text-align: center; border-bottom: 2px solid #E8A838; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #C4871A; font-size: 24px; }
        .header p { margin: 5px 0 0; color: #666; font-size: 12px; }
        .row { width: 100%; margin-bottom: 15px; }
        .col-half { width: 48%; display: inline-block; vertical-align: top; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f9f9f9; }
        .total-row td { font-weight: bold; }
        .text-right { text-align: right; }
        .status { padding: 5px 10px; border-radius: 4px; display: inline-block; font-weight: bold; }
        .status-confirmed { background: #dcfce7; color: #166534; }
        .footer { text-align: center; font-size: 11px; color: #999; margin-top: 40px; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎸 Rockstar Studio</h1>
        <p>Jl. Sendang Sari No.33, Gempol, Condongcatur, Sleman, DIY</p>
    </div>

    <div class="row">
        <div class="col-half">
            <strong>Nomor Booking:</strong> #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}<br>
            <strong>Tanggal Dibuat:</strong> {{ $booking->created_at->format('d/m/Y H:i') }}<br>
            <strong>Status:</strong> <span class="status status-{{ $booking->status }}">{{ strtoupper($booking->status) }}</span>
        </div>
        <div class="col-half">
            <strong>Pemesan:</strong> {{ $booking->booker_name }}<br>
            <strong>No HP:</strong> {{ $booking->booker_phone }}<br>
            @if($booking->band_name)
            <strong>Band:</strong> {{ $booking->band_name }}
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Deskripsi Pesanan</th>
                <th>Detail</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Studio</td>
                <td>{{ $booking->studio->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tanggal Sesi</td>
                <td>{{ $booking->date->translatedFormat('l, d F Y') }}</td>
            </tr>
            <tr>
                <td>Waktu Sesi</td>
                <td>Sesi {{ $booking->session_number }} ({{ $booking->start_time }} - {{ $booking->end_time }} WIB)</td>
            </tr>
            @if($booking->notes)
            <tr>
                <td>Catatan Tambahan</td>
                <td>{{ $booking->notes }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <table>
        <thead>
            <tr>
                <th>Rincian Biaya</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Harga Total Sesi (2 Jam)</td>
                <td class="text-right">Rp {{ number_format($booking->amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>DP Dibayarkan ({{ strtoupper(str_replace('_', ' ', $booking->payment_method)) }})</td>
                <td class="text-right" style="color:green;">- Rp {{ number_format($booking->dp_amount, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td>Sisa Pembayaran (Dibayar di Lokasi)</td>
                <td class="text-right">Rp {{ number_format($booking->amount - $booking->dp_amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Harap tunjukkan resi ini (digital/cetak) kepada petugas operator studio.<br>
        Dicetak pada {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
