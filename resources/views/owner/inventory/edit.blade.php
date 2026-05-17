@extends('layouts.app')
@section('title', __('messages.edit') . ' Inventaris')
@section('page-title', __('messages.edit') . ' Inventaris')
@section('content')
<div class="card" style="max-width:600px;">
    <form method="POST" action="{{ route('owner.inventory.update', $inventory) }}">
        @csrf @method('PUT')
        <div class="form-group"><label class="form-label">Studio</label><select name="studio_id" class="form-select" required>@foreach($studios as $s)<option value="{{ $s->id }}" {{ $inventory->studio_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>@endforeach</select></div>
        <div class="form-group"><label class="form-label">Nama Alat</label><input type="text" name="name" class="form-input" required value="{{ $inventory->name }}"></div>
        <div class="form-group"><label class="form-label">Kategori</label><select name="category" class="form-select" required><option value="alat_musik" {{ $inventory->category === 'alat_musik' ? 'selected' : '' }}>Alat Musik</option><option value="alat_rekaman" {{ $inventory->category === 'alat_rekaman' ? 'selected' : '' }}>Alat Rekaman</option><option value="alat_elektronik" {{ $inventory->category === 'alat_elektronik' ? 'selected' : '' }}>Alat Elektronik</option></select></div>
        <div class="form-group"><label class="form-label">Jumlah</label><input type="number" name="quantity" class="form-input" required min="1" value="{{ $inventory->quantity }}"></div>
        <div class="form-group"><label class="form-label">Kondisi</label><select name="condition" class="form-select" required><option value="baik" {{ $inventory->condition === 'baik' ? 'selected' : '' }}>Baik</option><option value="cukup" {{ $inventory->condition === 'cukup' ? 'selected' : '' }}>Cukup</option><option value="perlu_perbaikan" {{ $inventory->condition === 'perlu_perbaikan' ? 'selected' : '' }}>Perlu Perbaikan</option></select></div>
        <div class="form-group"><label class="form-label">Tanggal Pembelian</label><input type="date" name="purchase_date" class="form-input" value="{{ $inventory->purchase_date?->format('Y-m-d') }}"></div>
        <div class="form-group"><label class="form-label">Harga Beli (Rp)</label><input type="number" name="purchase_price" class="form-input" min="0" value="{{ $inventory->purchase_price }}"></div>
        <div class="form-group"><label class="form-label">Catatan</label><textarea name="notes" class="form-textarea" rows="2">{{ $inventory->notes }}</textarea></div>
        <div style="display:flex;gap:10px;"><button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button><a href="{{ route('owner.inventory.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a></div>
    </form>
</div>
@endsection
