@extends('layouts.auth')
@section('title', __('messages.login'))
@section('auth-content')
    <div class="auth-brand">
        <div class="icon">🎸</div>
        <h1>Rockstar Studio</h1>
        <p>{{ __('messages.login') }}</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <label class="form-label">{{ __('messages.email') }}</label>
            <input type="email" name="email" class="form-input" value="{{ old('email') }}" required autofocus placeholder="email@example.com">
            @error('email') <div class="error-text">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label class="form-label">{{ __('messages.password') }}</label>
            <input type="password" name="password" class="form-input" required placeholder="••••••••">
        </div>
        <div class="form-group" style="display:flex;align-items:center;gap:8px;">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember" style="font-size:14px;">{{ __('messages.remember_me') }}</label>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">{{ __('messages.login') }}</button>
    </form>
    <div class="auth-footer">
        Belum punya akun? <a href="{{ route('register') }}">{{ __('messages.register') }}</a>
    </div>
@endsection
