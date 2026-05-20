<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>New user pending approval</title>
</head>
<body style="font-family: system-ui, sans-serif; line-height: 1.6; color: #334155;">
    <p>Hello,</p>

    <p>A new user has registered on {{ config('app.name') }} and is waiting for <strong>agent / admin approval</strong>.</p>

    <table style="border-collapse: collapse; margin: 1rem 0;">
        <tr><td style="padding: 4px 12px 4px 0; color: #64748b;">User ID</td><td><strong>#{{ $user->id }}</strong></td></tr>
        <tr><td style="padding: 4px 12px 4px 0; color: #64748b;">Name</td><td>{{ $user->name }}</td></tr>
        <tr><td style="padding: 4px 12px 4px 0; color: #64748b;">Email</td><td>{{ $user->email }}</td></tr>
        <tr><td style="padding: 4px 12px 4px 0; color: #64748b;">Registered</td><td>{{ $user->created_at?->format('M d, Y g:i A') }}</td></tr>
        @if ($user->google_id)
            <tr><td style="padding: 4px 12px 4px 0; color: #64748b;">Sign-up</td><td>Google</td></tr>
        @endif
    </table>

    <p>
        <a href="{{ url('/admin/users/'.$user->id.'/edit') }}" style="display: inline-block; padding: 10px 16px; background: #4f46e5; color: #fff; text-decoration: none; border-radius: 6px;">
            Review in admin panel
        </a>
    </p>

    <p style="font-size: 0.875rem; color: #64748b;">— {{ config('app.name') }}</p>
</body>
</html>
