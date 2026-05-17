@extends('layouts.app')
@section('title', __('messages.dashboard'))
@section('page-title', __('messages.welcome') . ', ' . auth()->user()->name . '!')

@section('content')
    {{-- Event Carousel --}}
    @if($events->count())
        <div class="event-carousel">
            @foreach($events as $event)
                <div class="event-card">
                    <h3>{{ $event->title }}</h3>
                    <p>{{ $event->description }}</p>
                    <small>{{ $event->start_date->format('d M') }} - {{ $event->end_date->format('d M Y') }}</small>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Quick Actions --}}
    <div class="stat-cards">
        <a href="{{ route('renter.booking') }}" class="stat-card" style="text-decoration:none;cursor:pointer;">
            <div style="font-size:32px;margin-bottom:8px;">📅</div>
            <div class="stat-value" style="font-size:18px;">{{ __('booking.book_now') }}</div>
            <div class="stat-label">Lihat jadwal dan booking sesi studio</div>
        </a>
        <a href="{{ route('renter.my-bookings') }}" class="stat-card" style="text-decoration:none;cursor:pointer;">
            <div style="font-size:32px;margin-bottom:8px;">📋</div>
            <div class="stat-value" style="font-size:18px;">{{ __('messages.nav_my_bookings') }}</div>
            <div class="stat-label">Lihat dan kelola booking Anda</div>
        </a>
        <a href="{{ route('renter.studio.index') }}" class="stat-card" style="text-decoration:none;cursor:pointer;">
            <div style="font-size:32px;margin-bottom:8px;">🎵</div>
            <div class="stat-value" style="font-size:18px;">Daftar Studio</div>
            <div class="stat-label">Lihat fasilitas dan lokasi seluruh studio</div>
        </a>
    </div>

    {{-- Upcoming Bookings --}}
    @if($myBookings->count())
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">📅 Booking Mendatang</h3>
            </div>
            <table class="data-table">
                <thead><tr><th>{{ __('messages.date') }}</th><th>{{ __('booking.session') }}</th><th>Studio</th><th>{{ __('messages.status') }}</th></tr></thead>
                <tbody>
                    @foreach($myBookings as $booking)
                        <tr>
                            <td>{{ $booking->date->translatedFormat('l, d M Y') }}</td>
                            <td>Sesi {{ $booking->session_number }} ({{ $booking->start_time }}-{{ $booking->end_time }})</td>
                            <td>{{ $booking->studio->name ?? '-' }}</td>
                            <td><span class="badge badge-green">{{ __('booking.confirmed') }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
