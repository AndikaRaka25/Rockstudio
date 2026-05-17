@extends('layouts.app')
@section('title', __('messages.nav_events'))
@section('page-title', __('messages.nav_events'))
@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">📢 Daftar Event & Promo</h3><a href="{{ route('owner.events.create') }}" class="btn btn-primary btn-sm">+ {{ __('messages.add') }}</a></div>
    <table class="data-table">
        <thead><tr><th>Judul</th><th>Mulai</th><th>Berakhir</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse($events as $event)
                <tr>
                    <td style="font-weight:600;">{{ $event->title }}</td>
                    <td>{{ $event->start_date->format('d/m/Y') }}</td>
                    <td>{{ $event->end_date->format('d/m/Y') }}</td>
                    <td>
                        @if($event->is_active && $event->end_date >= now()->startOfDay())
                            <span class="badge badge-green">Aktif</span>
                        @else
                            <span class="badge badge-gray">Tidak Aktif</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('owner.events.edit', $event) }}" class="btn btn-secondary btn-sm">{{ __('messages.edit') }}</a>
                        <form method="POST" action="{{ route('owner.events.destroy', $event) }}" style="display:inline;" onsubmit="return confirm('Hapus event ini?')">@csrf @method('DELETE') <button class="btn btn-danger btn-sm">{{ __('messages.delete') }}</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;padding:24px;">{{ __('messages.no_data') }}</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination">{{ $events->links() }}</div>
</div>
@endsection
