@extends('layouts.auth')
@section('title', __('messages.register'))
@section('auth-content')
    <div class="auth-brand">
        <div class="icon">🎸</div>
        <h1>Rockstar Studio</h1>
        <p>{{ __('messages.register') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="form-group">
            <label class="form-label">{{ __('messages.name') }}</label>
            <input type="text" name="name" class="form-input" value="{{ old('name') }}" required placeholder="Nama lengkap">
            @error('name') <div class="error-text">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label class="form-label">{{ __('messages.email') }}</label>
            <input type="email" name="email" class="form-input" value="{{ old('email') }}" required placeholder="email@example.com">
            @error('email') <div class="error-text">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label class="form-label">{{ __('messages.phone') }}</label>
            <input type="text" name="phone" class="form-input" value="{{ old('phone') }}" required placeholder="08xxxxxxxxxx">
            @error('phone') <div class="error-text">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Daftar sebagai</label>
            <div class="role-selector">
                <div class="role-option">
                    <input type="radio" name="role" id="role-renter" value="renter" {{ old('role', 'renter') === 'renter' ? 'checked' : '' }}>
                    <label for="role-renter">🎤 Penyewa</label>
                </div>
                <div class="role-option">
                    <input type="radio" name="role" id="role-owner" value="owner" {{ old('role') === 'owner' ? 'checked' : '' }}>
                    <label for="role-owner">🏢 Pemilik</label>
                </div>
            </div>
            @error('role') <div class="error-text">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label class="form-label">{{ __('messages.password') }}</label>
            <input type="password" name="password" class="form-input" required placeholder="Min. 8 karakter">
            @error('password') <div class="error-text">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="form-input" required placeholder="Ulangi password">
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">{{ __('messages.register') }}</button>
    </form>
    <div class="auth-footer">
        Sudah punya akun? <a href="{{ route('login') }}">{{ __('messages.login') }}</a>
    </div>
@endsection
