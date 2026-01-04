<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="font-family: system-ui; padding: 24px; background:#f8fafc;">
    <div style="max-width: 420px; margin: 60px auto; background:#fff; padding:24px; border:1px solid #e5e7eb; border-radius:12px;">
        <h2 style="margin-bottom: 18px;">Login</h2>

        @if ($errors->any())
            <div style="background:#fee2e2; border:1px solid #fecaca; padding:12px; border-radius:10px; margin-bottom:16px;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   style="width:100%; padding:10px; margin:8px 0 14px; border:1px solid #d1d5db; border-radius:8px;">

            <label>Password</label>
            <input type="password" name="password" required
                   style="width:100%; padding:10px; margin:8px 0 14px; border:1px solid #d1d5db; border-radius:8px;">

            <label style="display:flex; gap:8px; align-items:center; margin-bottom:16px;">
                <input type="checkbox" name="remember">
                Remember me
            </label>

            <button type="submit"
                    style="width:100%; padding:10px; border:0; border-radius:10px; background:#111827; color:#fff; cursor:pointer;">
                Login
            </button>
        </form>
    </div>
</body>
</html>
