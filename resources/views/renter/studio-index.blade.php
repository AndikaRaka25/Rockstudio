@extends('layouts.app')
@section('title', 'Daftar Studio')
@section('page-title', 'Daftar Studio')

@section('content')
<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(300px, 1fr));gap:24px;">
    @foreach($studios as $studio)
    <div class="card" style="display: flex; flex-direction: column;">
        <h2 style="font-size:24px;margin-bottom:8px;">🎵 {{ $studio->name }}</h2>
        <p style="color:var(--color-text-secondary);margin-bottom:20px; flex-grow: 1;">{{ Str::limit($studio->description, 120) }}</p>
        
        <div style="margin-bottom:20px;">
            <strong>Rp {{ number_format($studio->price_per_session, 0, ',', '.') }}</strong> / sesi
        </div>
        
        <a href="{{ route('renter.studio.detail', $studio) }}" class="btn btn-primary" style="width: 100%; justify-content: center;">
            Lihat Detail & Fasilitas
        </a>
    </div>
    @endforeach
</div>
@endsection
