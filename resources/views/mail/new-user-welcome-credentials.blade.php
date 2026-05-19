<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Welcome to {{ config('app.name') }}</title>
</head>
<body style="font-family: system-ui, sans-serif; line-height: 1.6; color: #334155;">
    <p>Hi {{ $user->name }},</p>

    <p>Thank you for registering with {{ config('app.name') }}. Your account has been created and is <strong>pending administrator approval</strong>. You will be able to use the dashboard once an admin approves your account and sets your membership.</p>

    <p style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px;">
        <strong>Your login details</strong><br><br>
        <strong>Login ID (email):</strong> {{ $user->email }}<br>
        <strong>User ID:</strong> #{{ $user->id }}<br>
        <strong>Password:</strong> {{ $plainPassword }}
    </p>

  <p>You can sign in at <a href="{{ route('login') }}">{{ route('login') }}</a>. We recommend changing your password after your first login from Settings.</p>

    @if ($user->google_id)
        <p>You may also continue to use <strong>Sign in with Google</strong> on the login page.</p>
    @endif

    <p style="font-size: 0.875rem; color: #64748b;">If you did not create this account, please contact support.</p>

    <p style="font-size: 0.875rem; color: #64748b;">— {{ config('app.name') }} team</p>
</body>
</html>
