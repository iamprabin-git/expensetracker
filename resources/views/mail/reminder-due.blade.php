<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Reminder</title>
</head>
<body style="font-family: system-ui, sans-serif; line-height: 1.6; color: #334155;">
    <p>Hi {{ $reminder->user->name }},</p>

    <p>This is your scheduled reminder from {{ config('app.name') }}.</p>

    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin: 16px 0;">
        <p style="margin: 0 0 8px;"><strong>{{ $reminder->title }}</strong></p>
        <p style="margin: 0 0 4px; color: #64748b;">Type: {{ $reminder->type->label() }}</p>
        @if ($reminder->payee_name)
            <p style="margin: 0 0 4px;">Payee / creditor: <strong>{{ $reminder->payee_name }}</strong></p>
        @endif
        @if ($reminder->amount !== null)
            <p style="margin: 0 0 4px;">Amount: <strong>{{ $reminder->formattedAmount() }}</strong></p>
        @endif
        <p style="margin: 0 0 4px;">Due: <strong>{{ $reminder->next_remind_at->timezone($reminder->user->timezone ?? config('app.timezone'))->format('M d, Y g:i A') }}</strong></p>
        <p style="margin: 0;">Repeats: {{ $reminder->frequency->label() }}</p>
    </div>

    @if ($reminder->notes)
        <p><strong>Notes:</strong><br>{!! nl2br(e($reminder->notes)) !!}</p>
    @endif

    <p>
        <a href="{{ route('reminders.index') }}" style="display: inline-block; padding: 10px 16px; background: #4f46e5; color: #fff; text-decoration: none; border-radius: 6px;">
            View reminders
        </a>
    </p>

    <p style="font-size: 0.875rem; color: #64748b;">— {{ config('app.name') }}</p>
</body>
</html>
