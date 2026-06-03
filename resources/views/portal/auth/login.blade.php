<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ __('portal.login') }} — {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Tajawal', sans-serif; background: #f0f4f8; display: flex; min-height: 100vh; align-items: center; justify-content: center; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,.08); padding: 2.5rem; width: 100%; max-width: 420px; }
        .logo { text-align: center; margin-bottom: 2rem; }
        .logo h1 { color: #1E3A5F; font-size: 1.8rem; font-weight: 700; }
        .logo p { color: #64748b; font-size: .9rem; margin-top: .25rem; }
        label { display: block; font-size: .9rem; color: #374151; margin-bottom: .4rem; font-weight: 500; }
        input[type=email], input[type=password] { width: 100%; padding: .65rem 1rem; border: 1px solid #d1d5db; border-radius: 8px; font-family: inherit; font-size: 16px; outline: none; transition: border .2s; }
        input:focus { border-color: #1E3A5F; }
        .field { margin-bottom: 1.2rem; }
        .error { color: #ef4444; font-size: .85rem; margin-top: .3rem; }
        .btn { width: 100%; padding: .75rem; background: #1E3A5F; color: #fff; border: none; border-radius: 8px; font-family: inherit; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background .2s; }
        .btn:hover { background: #162d4a; }
        .remember { display: flex; align-items: center; gap: .5rem; margin-bottom: 1.2rem; font-size: .9rem; color: #374151; }
        @media (max-width: 460px) {
            body { align-items: flex-start; padding-top: 2rem; }
            .card { padding: 1.75rem 1.25rem; margin: 0 .75rem; }
        }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">
        <h1>{{ config('app.name') }}</h1>
        <p>{{ __('portal.client_portal') }}</p>
    </div>

    <form method="POST" action="{{ route('portal.login.post') }}">
        @csrf

        <div class="field">
            <label for="email">{{ __('portal.email') }}</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label for="password">{{ __('portal.password') }}</label>
            <input type="password" id="password" name="password" required>
            @error('password') <p class="error">{{ $message }}</p> @enderror
        </div>

        <label class="remember">
            <input type="checkbox" name="remember"> {{ __('portal.remember_me') }}
        </label>

        <button type="submit" class="btn">{{ __('portal.sign_in') }}</button>
    </form>
</div>
</body>
</html>
