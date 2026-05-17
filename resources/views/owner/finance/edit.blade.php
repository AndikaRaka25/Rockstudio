@extends('layouts.app')
@section('title', __('messages.edit') . ' Transaksi')
@section('page-title', __('messages.edit') . ' Transaksi')
@section('content')
<div class="card" style="max-width:600px;">
    <form method="POST" action="{{ route('owner.finance.update', $finance) }}">
        @csrf @method('PUT')
        <div class="form-group"><label class="form-label">Studio</label><select name="studio_id" class="form-select" required>@foreach($studios as $s)<option value="{{ $s->id }}" {{ $finance->studio_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>@endforeach</select></div>
        <div class="form-group"><label class="form-label">Tipe</label><select name="type" class="form-select" required><option value="income" {{ $finance->type === 'income' ? 'selected' : '' }}>Pemasukan</option><option value="expense" {{ $finance->type === 'expense' ? 'selected' : '' }}>Pengeluaran</option></select></div>
        <div class="form-group"><label class="form-label">Kategori</label><input type="text" name="category" class="form-input" required value="{{ $finance->category }}"></div>
        <div class="form-group"><label class="form-label">Jumlah (Rp)</label><input type="number" name="amount" class="form-input" required min="1" value="{{ $finance->amount }}"></div>
        <div class="form-group"><label class="form-label">Deskripsi</label><input type="text" name="description" class="form-input" required value="{{ $finance->description }}"></div>
        <div class="form-group"><label class="form-label">Tanggal</label><input type="date" name="date" class="form-input" required value="{{ $finance->date->format('Y-m-d') }}"></div>
        <div style="display:flex;gap:10px;"><button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button><a href="{{ route('owner.finance.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a></div>
    </form>
</div>
@endsection
