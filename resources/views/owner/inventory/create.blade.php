@extends('layouts.app')
@section('title', __('messages.add') . ' Inventaris')
@section('page-title', __('messages.add') . ' Inventaris')
@section('content')
<div class="card" style="max-width:600px;">
    <form method="POST" action="{{ route('owner.inventory.store') }}">
        @csrf
        <div class="form-group"><label class="form-label">Studio</label><select name="studio_id" class="form-select" required>@foreach($studios as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
        <div class="form-group"><label class="form-label">Nama Alat</label><input type="text" name="name" class="form-input" required placeholder="Contoh: Drum Set Yamaha"></div>
        <div class="form-group"><label class="form-label">Kategori</label><select name="category" class="form-select" required><option value="alat_musik">Alat Musik</option><option value="alat_rekaman">Alat Rekaman</option><option value="alat_elektronik">Alat Elektronik</option></select></div>
        <div class="form-group"><label class="form-label">Jumlah</label><input type="number" name="quantity" class="form-input" required min="1" value="1"></div>
        <div class="form-group"><label class="form-label">Kondisi</label><select name="condition" class="form-select" required><option value="baik">Baik</option><option value="cukup">Cukup</option><option value="perlu_perbaikan">Perlu Perbaikan</option></select></div>
        <div class="form-group"><label class="form-label">Tanggal Pembelian</label><input type="date" name="purchase_date" class="form-input"></div>
        <div class="form-group"><label class="form-label">Harga Beli (Rp)</label><input type="number" name="purchase_price" class="form-input" min="0"></div>
        <div class="form-group"><label class="form-label">Catatan</label><textarea name="notes" class="form-textarea" rows="2"></textarea></div>
        <div style="display:flex;gap:10px;"><button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button><a href="{{ route('owner.inventory.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a></div>
    </form>
</div>
@endsection
