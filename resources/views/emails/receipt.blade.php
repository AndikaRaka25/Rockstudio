<!DOCTYPE html>
<html>
<head>
    <title>Resi Booking Rockstar Studio</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Halo, {{ $booking->booker_name }}!</h2>
    <p>Terima kasih telah melakukan pemesanan di Rockstar Studio.</p>
    <p>Pemesanan Anda untuk <strong>{{ $booking->studio->name ?? 'Studio' }}</strong> pada tanggal <strong>{{ $booking->date->translatedFormat('d M Y') }}</strong> telah <strong>{{ strtoupper($booking->status) }}</strong>.</p>
    
    <p>Bersama email ini, kami lampirkan file PDF berisi Resi Booking Anda yang dapat digunakan sebagai bukti pemesanan di lokasi studio.</p>
    
    <br>
    <p>Salam hangat,<br><strong>Rockstar Studio Team</strong></p>
</body>
</html>
