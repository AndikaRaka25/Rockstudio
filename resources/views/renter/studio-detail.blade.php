@extends('layouts.app')
@section('title', $studio->name . ' — ' . __('messages.nav_studio'))
@section('page-title', __('messages.nav_studio'))

@section('content')
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
    {{-- Studio Info --}}
    <div class="card">
        <h2 style="font-size:28px;margin-bottom:8px;">🎵 {{ $studio->name }}</h2>
        <p style="color:var(--color-text-secondary);margin-bottom:20px;">{{ $studio->description }}</p>

        <div style="margin-bottom:20px;">
            <h4 style="margin-bottom:8px;">💰 Harga</h4>
            <p><strong>Rp {{ number_format($studio->price_per_session, 0, ',', '.') }}</strong> / sesi (2 jam)</p>
            <p>DP: <strong>Rp {{ number_format($studio->dp_amount, 0, ',', '.') }}</strong></p>
        </div>

        <div style="margin-bottom:20px;">
            <h4 style="margin-bottom:8px;">⏰ Jam Operasional</h4>
            <p>{{ $studio->operating_start }} — {{ $studio->operating_end }} WIB</p>
            <p>8 Sesi × 2 Jam</p>
        </div>

        <div style="margin-bottom:20px;">
            <h4 style="margin-bottom:8px;">🎸 Fasilitas</h4>
            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                @foreach($studio->facilities ?? [] as $facility)
                    <span class="badge badge-blue">{{ $facility }}</span>
                @endforeach
            </div>
        </div>

        <a href="{{ route('renter.booking', $studio) }}" class="btn btn-primary">📅 {{ __('booking.book_now') }}</a>
    </div>

    {{-- Gallery & Map --}}
    <div>
        <div class="card" style="margin-bottom:20px; position:relative; padding:0; overflow:hidden;">
            @php
                // Fallback dummy images if DB is empty
                $gallery = !empty($studio->images) ? $studio->images : [
                    'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?q=80&w=800&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1514320291840-2e0a9bf2a9ae?q=80&w=800&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1621619856624-42fd193a0661?q=80&w=800&auto=format&fit=crop',
                ];
            @endphp
            <div id="carousel" style="display:flex; overflow-x:auto; scroll-snap-type:x mandatory; scroll-behavior:smooth; -ms-overflow-style:none; scrollbar-width:none; height:250px;">
                @foreach($gallery as $img)
                    <img src="{{ $img }}" alt="Studio Image" style="width:100%; height:100%; object-fit:cover; flex:none; scroll-snap-align:start;">
                @endforeach
            </div>
            <button onclick="document.getElementById('carousel').scrollBy({left: -300, behavior: 'smooth'})" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); background:rgba(0,0,0,0.5); color:#fff; border:none; width:36px; height:36px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:18px;">❮</button>
            <button onclick="document.getElementById('carousel').scrollBy({left: 300, behavior: 'smooth'})" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); background:rgba(0,0,0,0.5); color:#fff; border:none; width:36px; height:36px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:18px;">❯</button>
        </div>

        <div class="card" style="margin-bottom:20px;">
            <h4 style="margin-bottom:12px;">📍 Lokasi</h4>
            <div id="studio-map" class="studio-map"></div>
            <p style="margin-top:12px;font-size:14px;color:var(--color-text-secondary);">{{ $studio->address }}</p>
            <a href="{{ $studio->google_maps_url }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm" style="margin-top:10px;">
                🗺️ Buka di Google Maps
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var map = L.map('studio-map').setView([{{ $studio->latitude }}, {{ $studio->longitude }}], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        L.marker([{{ $studio->latitude }}, {{ $studio->longitude }}])
            .addTo(map)
            .bindPopup('<b>{{ $studio->name }}</b><br>{{ $studio->address }}')
            .openPopup();
    });
</script>
@endpush
@endsection
