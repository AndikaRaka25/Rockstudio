@extends('layouts.app')
@section('title', 'Detail Pesanan')
@section('page-title', 'Detail Pesanan')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header" style="border-bottom: 1px solid var(--color-border); padding-bottom: 16px; margin-bottom: 16px;">
        <h3 class="card-title">🧾 Booking #{{ $booking->id }}</h3>
        <span class="badge {{ match($booking->status) { 'confirmed' => 'badge-green', 'pending' => 'badge-yellow', 'cancelled' => 'badge-red', 'expired' => 'badge-gray', default => 'badge-gray' } }}" style="font-size: 14px; padding: 6px 12px;">
            {{ __('booking.' . $booking->status) }}
        </span>
    </div>

    <div class="booking-summary" style="background: none; padding: 0;">
        <div class="summary-row" style="margin-bottom: 12px;">
            <span class="label">Tanggal Booking</span>
            <span class="value">{{ $booking->date->translatedFormat('l, d F Y') }}</span>
        </div>
        <div class="summary-row" style="margin-bottom: 12px;">
            <span class="label">Waktu / Sesi</span>
            <span class="value">Sesi {{ $booking->session_number }} ({{ $booking->start_time }} - {{ $booking->end_time }} WIB)</span>
        </div>
        <div class="summary-row" style="margin-bottom: 12px;">
            <span class="label">Studio</span>
            <span class="value">{{ $booking->studio->name ?? '-' }}</span>
        </div>
        <div class="summary-row" style="margin-bottom: 12px;">
            <span class="label">Nama Pemesan</span>
            <span class="value">{{ $booking->booker_name }}</span>
        </div>
        @if($booking->band_name)
        <div class="summary-row" style="margin-bottom: 12px;">
            <span class="label">Nama Band</span>
            <span class="value">{{ $booking->band_name }}</span>
        </div>
        @endif
        @if($booking->notes)
        <div class="summary-row" style="margin-bottom: 12px;">
            <span class="label">Catatan</span>
            <span class="value" style="text-align: right;">{{ $booking->notes }}</span>
        </div>
        @endif
        
        <div style="border-top: 1px dashed var(--color-border); margin: 16px 0;"></div>
        
        <div class="summary-row" style="margin-bottom: 12px;">
            <span class="label">Metode Pembayaran</span>
            <span class="value">{{ $booking->payment_method ? strtoupper(str_replace('_', ' ', $booking->payment_method)) : '-' }}</span>
        </div>
        <div class="summary-row" style="margin-bottom: 12px;">
            <span class="label">Total Harga Sesi</span>
            <span class="value">Rp {{ number_format($booking->amount, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row" style="margin-bottom: 12px;">
            <span class="label">DP yang Dibayar</span>
            <span class="value" style="color: var(--success); font-weight: 700;">Rp {{ number_format($booking->dp_amount, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row" style="margin-bottom: 12px;">
            <span class="label">Sisa Pelunasan di Lokasi</span>
            <span class="value" style="color: var(--error); font-weight: 700;">Rp {{ number_format($booking->amount - $booking->dp_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    @if($booking->status === 'pending')
        <div class="alert alert-warning" style="margin-top: 20px;">
            Sesi Anda telah di-*lock*. Segera lakukan pembayaran DP sebelum waktu habis agar booking Anda tidak dibatalkan otomatis.
        </div>
        <div style="display:flex;gap:10px;margin-top:20px;">
            <a href="{{ route('renter.booking', $booking->studio_id) }}" class="btn btn-primary" style="flex:1;justify-content:center;">
                Bayar Sekarang / Buka Jadwal
            </a>
        </div>
    @endif

    <div style="margin-top: 24px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; align-items: center;">
        <a href="{{ route('renter.booking.pdf', $booking) }}" class="btn btn-primary" style="height: 42px;">📄 Cetak Resi PDF</a>
        
        <form action="{{ route('renter.booking.email', $booking) }}" method="POST" style="display: flex; gap: 8px;">
            @csrf
            <input type="email" name="target_email" class="form-input" placeholder="Masukkan alamat email..." required value="{{ auth()->user()->email }}" style="width: 220px; height: 42px;">
            <button type="submit" class="btn btn-secondary" style="height: 42px;">✉️ Kirim</button>
        </form>

        <a href="{{ route('renter.my-bookings') }}" class="btn btn-secondary" style="height: 42px;">Kembali ke Daftar</a>
    </div>
</div>
@endsection
