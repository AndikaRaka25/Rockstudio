<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="{{ session('theme', auth()->user()->theme ?? 'light') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Rockstar Studio') — {{ __('messages.app_name') }}</title>
    <meta name="description" content="Reservasi studio musik online - Rockstar Studio Yogyakarta">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body>
    <div class="app-layout">
        {{-- Sidebar --}}
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon">🎸</div>
                <div>
                    <h1>Rockstar</h1>
                    <span>Studio</span>
                </div>
            </div>
            <nav class="sidebar-nav">
                @if(auth()->user()->isRenter())
                    <div class="nav-section">
                        <div class="nav-section-title">Menu</div>
                        <a href="{{ route('renter.dashboard') }}" class="nav-link {{ request()->routeIs('renter.dashboard') ? 'active' : '' }}">
                            <span class="nav-icon">🏠</span> {{ __('messages.dashboard') }}
                        </a>
                        <a href="{{ route('renter.booking') }}" class="nav-link {{ request()->routeIs('renter.booking') ? 'active' : '' }}">
                            <span class="nav-icon">📅</span> {{ __('messages.nav_booking') }}
                        </a>
                        <a href="{{ route('renter.my-bookings') }}" class="nav-link {{ request()->routeIs('renter.my-bookings') ? 'active' : '' }}">
                            <span class="nav-icon">📋</span> {{ __('messages.nav_my_bookings') }}
                        </a>
                        <a href="{{ route('renter.studio.index') }}" class="nav-link {{ request()->routeIs('renter.studio.*') ? 'active' : '' }}">
                            <span class="nav-icon">🎵</span> Daftar Studio
                        </a>
                    </div>
                @else
                    <div class="nav-section">
                        <div class="nav-section-title">{{ __('messages.dashboard') }}</div>
                        <a href="{{ route('owner.dashboard') }}" class="nav-link {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
                            <span class="nav-icon">🏠</span> {{ __('messages.dashboard') }}
                        </a>
                        <a href="{{ route('owner.schedule') }}" class="nav-link {{ request()->routeIs('owner.schedule') ? 'active' : '' }}">
                            <span class="nav-icon">📅</span> {{ __('messages.nav_schedule') }}
                        </a>
                    </div>
                    <div class="nav-section">
                        <div class="nav-section-title">Manajemen</div>
                        <a href="{{ route('owner.bookings.index') }}" class="nav-link {{ request()->routeIs('owner.bookings.*') ? 'active' : '' }}">
                            <span class="nav-icon">📋</span> {{ __('messages.nav_manage_bookings') }}
                        </a>
                        <a href="{{ route('owner.finance.index') }}" class="nav-link {{ request()->routeIs('owner.finance.*') ? 'active' : '' }}">
                            <span class="nav-icon">💰</span> {{ __('messages.nav_finance') }}
                        </a>
                        <a href="{{ route('owner.inventory.index') }}" class="nav-link {{ request()->routeIs('owner.inventory.*') ? 'active' : '' }}">
                            <span class="nav-icon">🎸</span> {{ __('messages.nav_inventory') }}
                        </a>
                        <a href="{{ route('owner.events.index') }}" class="nav-link {{ request()->routeIs('owner.events.*') ? 'active' : '' }}">
                            <span class="nav-icon">📢</span> {{ __('messages.nav_events') }}
                        </a>
                    </div>
                @endif
            </nav>
        </aside>

        {{-- Main Content --}}
        <div class="main-content">
            {{-- Header --}}
            <header class="top-header">
                <div class="header-left">
                    <button class="mobile-menu-btn" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</button>
                    <h2>@yield('page-title', __('messages.dashboard'))</h2>
                </div>
                <div class="header-right">
                    {{-- Language Switcher --}}
                    <a href="{{ route('locale.switch', app()->getLocale() === 'id' ? 'en' : 'id') }}" class="header-btn" title="{{ __('messages.switch_language') }}">
                        {{ app()->getLocale() === 'id' ? '🇬🇧 EN' : '🇮🇩 ID' }}
                    </a>
                    {{-- Theme Toggle --}}
                    <a href="{{ route('theme.switch', session('theme', 'light') === 'light' ? 'dark' : 'light') }}" class="header-btn" title="{{ __('messages.switch_theme') }}">
                        {{ session('theme', 'light') === 'light' ? '🌙' : '☀️' }}
                    </a>
                    {{-- User Menu --}}
                    <div class="user-menu">
                        <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                        <span class="user-name">{{ auth()->user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="header-btn">{{ __('messages.logout') }}</button>
                    </form>
                </div>
            </header>

            {{-- Flash Messages --}}
            <div class="page-content">
                @if(session('success'))
                    <div class="alert alert-success">✅ {{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error">❌ {{ session('error') }}</div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    @livewireScripts
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @stack('scripts')
</body>
</html>
