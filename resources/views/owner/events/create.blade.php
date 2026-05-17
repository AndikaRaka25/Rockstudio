@extends('layouts.app')
@section('title', __('messages.add') . ' Event')
@section('page-title', __('messages.add') . ' Event')
@section('content')
<div class="card" style="max-width:600px;">
    <form method="POST" action="{{ route('owner.events.store') }}">
        @csrf
        <div class="form-group"><label class="form-label">Studio</label><select name="studio_id" class="form-select" required>@foreach($studios as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
        <div class="form-group"><label class="form-label">Judul Event/Promo</label><input type="text" name="title" class="form-input" required placeholder="Contoh: Promo Sesi Malam"></div>
        <div class="form-group"><label class="form-label">Deskripsi Singkat</label><textarea name="description" class="form-textarea" rows="3"></textarea></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group"><label class="form-label">Tanggal Mulai</label><input type="date" name="start_date" class="form-input" required value="{{ date('Y-m-d') }}"></div>
            <div class="form-group"><label class="form-label">Tanggal Berakhir</label><input type="date" name="end_date" class="form-input" required value="{{ date('Y-m-d', strtotime('+7 days')) }}"></div>
        </div>
        <div class="form-group" style="display:flex;align-items:center;gap:8px;">
            <input type="checkbox" name="is_active" id="is_active" value="1" checked>
            <label for="is_active" style="font-size:14px;font-weight:600;">Event Aktif</label>
        </div>
        <div style="display:flex;gap:10px;margin-top:20px;"><button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button><a href="{{ route('owner.events.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a></div>
    </form>
</div>
@endsection
