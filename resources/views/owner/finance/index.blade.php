@extends('layouts.app')
@section('title', __('messages.nav_finance'))
@section('page-title', __('messages.nav_finance'))
@section('content')
<div class="stat-cards">
    <div class="stat-card"><div class="stat-value" style="color:var(--success);">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div><div class="stat-label">💰 Total Pemasukan</div></div>
    <div class="stat-card"><div class="stat-value" style="color:var(--error);">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div><div class="stat-label">📤 Total Pengeluaran</div></div>
    <div class="stat-card"><div class="stat-value">Rp {{ number_format($totalIncome - $totalExpense, 0, ',', '.') }}</div><div class="stat-label">📊 Saldo</div></div>
</div>
<div class="card">
    <div class="card-header"><h3 class="card-title">📋 Riwayat Transaksi</h3><a href="{{ route('owner.finance.create') }}" class="btn btn-primary btn-sm">+ {{ __('messages.add') }}</a></div>
    <table class="data-table">
        <thead><tr><th>Tanggal</th><th>Tipe</th><th>Kategori</th><th>Deskripsi</th><th>Jumlah</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse($transactions as $t)
                <tr>
                    <td>{{ $t->date->format('d/m/Y') }}</td>
                    <td><span class="badge {{ $t->type === 'income' ? 'badge-green' : 'badge-red' }}">{{ ucfirst($t->type) }}</span></td>
                    <td>{{ $t->category }}</td>
                    <td>{{ $t->description }}</td>
                    <td style="font-weight:600;">Rp {{ number_format($t->amount, 0, ',', '.') }}</td>
                    <td>
                        <a href="{{ route('owner.finance.edit', $t) }}" class="btn btn-secondary btn-sm">{{ __('messages.edit') }}</a>
                        <form method="POST" action="{{ route('owner.finance.destroy', $t) }}" style="display:inline;" onsubmit="return confirm('Hapus transaksi ini?')">@csrf @method('DELETE') <button class="btn btn-danger btn-sm">{{ __('messages.delete') }}</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center;padding:24px;">{{ __('messages.no_data') }}</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination">{{ $transactions->links() }}</div>
</div>
@endsection
