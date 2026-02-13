<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SK Account Created</title>
</head>
<body style="font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; color:#111">
    <div style="max-width:600px;margin:40px auto;border:1px solid #e5e7eb;padding:24px;border-radius:8px;">
        <div style="text-align:center;padding-bottom:12px;">
            <img src="{{ asset('images/LydoLogo.png') }}" alt="Lydo Logo" style="height:64px;object-fit:contain;">
        </div>

        <h2 style="font-weight:700;color:#111">Your SK account has been created</h2>
        <p style="color:#374151">Hello {{ $user->name }},</p>
        <p style="color:#374151">An SK account has been created for you and assigned to <strong>{{ $user->barangay }}</strong>. You can log in using your email and the password below. For security, change your password after first login.</p>

        <div style="background:#f8fafc;padding:12px;border-radius:6px;margin-top:12px;border:1px solid #e6eef8;">
            <p style="margin:0;color:#111"><strong>Email:</strong> {{ $user->email }}</p>
            <p style="margin:0;color:#111"><strong>Password:</strong> {{ $password }}</p>
        </div>

        <p style="margin-top:12px;color:#374151">Login here: <a href="{{ url('/login') }}">{{ url('/login') }}</a></p>

        <p style="color:#9ca3af;font-size:12px;margin-top:14px">If you did not expect this account, contact your admin.</p>
    </div>
</body>
</html>
