@extends('layouts.app')
@section('title', __('messages.dashboard'))
@section('page-title', __('messages.dashboard') . ' — Owner')

@section('content')
    <div class="stat-cards">
        <div class="stat-card">
            <div class="stat-value">{{ $todayBookings }}</div>
            <div class="stat-label">📅 Booking Hari Ini</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">Rp {{ number_format($monthIncome, 0, ',', '.') }}</div>
            <div class="stat-label">💰 Pendapatan Bulan Ini</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $bookedThisWeek }} / {{ $totalSessions }}</div>
            <div class="stat-label">📊 Sesi Terbooking Minggu Ini</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $studios->count() }}</div>
            <div class="stat-label">🎵 Jumlah Studio</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">📋 Booking Terbaru</h3>
            <a href="{{ route('owner.bookings.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
        </div>
        <table class="data-table">
            <thead><tr><th>Tanggal</th><th>Sesi</th><th>Studio</th><th>Pemesan</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($recentBookings as $b)
                    <tr>
                        <td>{{ $b->date->format('d/m/Y') }}</td>
                        <td>Sesi {{ $b->session_number }}</td>
                        <td>{{ $b->studio->name ?? '-' }}</td>
                        <td>{{ $b->booker_name }}</td>
                        <td><span class="badge {{ $b->status === 'confirmed' ? 'badge-green' : 'badge-yellow' }}">{{ ucfirst($b->status) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center;padding:24px;">{{ __('messages.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
