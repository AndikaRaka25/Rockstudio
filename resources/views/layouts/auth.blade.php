<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="{{ session('theme', 'light') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — Rockstar Studio</title>
    @vite(['resources/css/app.css'])
    <style>
        .auth-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--color-bg) 0%, var(--color-bg-alt) 100%); padding: 20px; }
        .auth-card { background: var(--color-bg-card); border-radius: var(--radius-xl); padding: 40px; width: 100%; max-width: 440px; box-shadow: var(--shadow-lg); border: 1px solid var(--color-border); }
        .auth-brand { text-align: center; margin-bottom: 32px; }
        .auth-brand .icon { width: 60px; height: 60px; margin: 0 auto 16px; background: linear-gradient(135deg, var(--color-primary), var(--color-accent)); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; font-size: 28px; }
        .auth-brand h1 { font-size: 24px; font-family: var(--font-heading); }
        .auth-brand p { color: var(--color-text-secondary); font-size: 14px; margin-top: 4px; }
        .auth-footer { text-align: center; margin-top: 24px; font-size: 14px; }
        .auth-footer a { color: var(--color-primary-dark); font-weight: 600; }
        .error-text { color: var(--error); font-size: 13px; margin-top: 4px; }
        .role-selector { display: flex; gap: 10px; margin-bottom: 16px; }
        .role-option { flex: 1; }
        .role-option input[type="radio"] { display: none; }
        .role-option label { display: block; padding: 14px; text-align: center; border: 2px solid var(--color-border); border-radius: var(--radius-md); cursor: pointer; font-weight: 600; font-size: 14px; transition: all var(--transition); }
        .role-option input:checked + label { border-color: var(--color-primary); background: var(--color-primary-glow); color: var(--color-primary-dark); }
    </style>
</head>
<body>
    <div class="auth-page">
        <div class="auth-card">
            @yield('auth-content')
        </div>
    </div>
</body>
</html>
