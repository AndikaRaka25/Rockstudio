@extends('layouts.app')
@section('title', __('messages.nav_inventory'))
@section('page-title', __('messages.nav_inventory'))
@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">🎸 Daftar Inventaris</h3><a href="{{ route('owner.inventory.create') }}" class="btn btn-primary btn-sm">+ {{ __('messages.add') }}</a></div>
    <table class="data-table">
        <thead><tr><th>Nama</th><th>Kategori</th><th>Jumlah</th><th>Kondisi</th><th>Studio</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td style="font-weight:600;">{{ $item->name }}</td>
                    <td><span class="badge badge-blue">{{ $item->category_label }}</span></td>
                    <td>{{ $item->quantity }}</td>
                    <td><span class="badge badge-{{ $item->condition_color }}">{{ ucfirst(str_replace('_', ' ', $item->condition)) }}</span></td>
                    <td>{{ $item->studio->name ?? '-' }}</td>
                    <td>
                        <a href="{{ route('owner.inventory.edit', $item) }}" class="btn btn-secondary btn-sm">{{ __('messages.edit') }}</a>
                        <form method="POST" action="{{ route('owner.inventory.destroy', $item) }}" style="display:inline;" onsubmit="return confirm('Hapus item ini?')">@csrf @method('DELETE') <button class="btn btn-danger btn-sm">{{ __('messages.delete') }}</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center;padding:24px;">{{ __('messages.no_data') }}</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination">{{ $items->links() }}</div>
</div>
@endsection
