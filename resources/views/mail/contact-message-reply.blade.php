<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Reply from {{ config('app.name') }}</title>
</head>
<body style="font-family: system-ui, sans-serif; line-height: 1.6; color: #334155;">
    <p>Hi {{ $contactMessage->name }},</p>

    <p>{!! nl2br(e($contactMessage->admin_reply)) !!}</p>

    <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 1.5rem 0;">

    <p style="font-size: 0.875rem; color: #64748b;">
        <strong>Your message:</strong><br>
        <em>Subject:</em> {{ $contactMessage->subject }}<br>
        {!! nl2br(e($contactMessage->message)) !!}
    </p>

    <p style="font-size: 0.875rem; color: #64748b;">— {{ config('app.name') }} team</p>
</body>
</html>
