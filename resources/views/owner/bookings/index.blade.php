@extends('layouts.app')
@section('title', __('messages.nav_manage_bookings'))
@section('page-title', __('messages.nav_manage_bookings'))
@section('content')
<div class="card">
    <table class="data-table">
        <thead><tr><th>ID</th><th>Tanggal</th><th>Sesi</th><th>Studio</th><th>Pemesan</th><th>Band</th><th>Status</th><th>Pembayaran</th></tr></thead>
        <tbody>
            @forelse($bookings as $b)
                <tr>
                    <td>#{{ $b->id }}</td>
                    <td>{{ $b->date->format('d/m/Y') }}</td>
                    <td>Sesi {{ $b->session_number }}</td>
                    <td>{{ $b->studio->name ?? '-' }}</td>
                    <td>{{ $b->booker_name }}</td>
                    <td>{{ $b->band_name ?: '-' }}</td>
                    <td><span class="badge {{ match($b->status) { 'confirmed'=>'badge-green','pending'=>'badge-yellow','cancelled'=>'badge-red',default=>'badge-gray' } }}">{{ ucfirst($b->status) }}</span></td>
                    <td>{{ $b->payment_method ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center;padding:24px;">{{ __('messages.no_data') }}</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination">{{ $bookings->links() }}</div>
</div>
@endsection
