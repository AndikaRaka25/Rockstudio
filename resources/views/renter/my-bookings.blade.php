@extends('layouts.app')
@section('title', __('messages.nav_my_bookings'))
@section('page-title', __('messages.nav_my_bookings'))

@section('content')
<div class="card">
    <table class="data-table">
        <thead><tr><th>{{ __('messages.date') }}</th><th>{{ __('booking.session') }}</th><th>Studio</th><th>Band</th><th>{{ __('messages.status') }}</th><th>{{ __('messages.actions') }}</th></tr></thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>{{ $booking->date->format('d/m/Y') }}</td>
                    <td>Sesi {{ $booking->session_number }} ({{ $booking->start_time }}-{{ $booking->end_time }})</td>
                    <td>{{ $booking->studio->name ?? '-' }}</td>
                    <td>{{ $booking->band_name ?: '-' }}</td>
                    <td>
                        <span class="badge {{ match($booking->status) { 'confirmed' => 'badge-green', 'pending' => 'badge-yellow', 'cancelled' => 'badge-red', 'expired' => 'badge-gray', default => 'badge-gray' } }}">
                            {{ __('booking.' . $booking->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('renter.booking.show', $booking) }}" class="btn btn-secondary btn-sm">Lihat Detail</a>
                        @if($booking->canCancel())
                            <div style="margin-top:4px;"><span style="font-size:11px;color:var(--color-text-secondary);">{{ __('booking.h1_warning') }}</span></div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center;padding:32px;">{{ __('messages.no_data') }}</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination">{{ $bookings->links() }}</div>
</div>
@endsection
