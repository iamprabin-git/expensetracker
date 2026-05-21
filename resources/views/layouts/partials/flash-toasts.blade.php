@php
    $toasts = collect();

    if (session('success')) {
        $toasts->push(['type' => 'success', 'message' => session('success')]);
    }

    if (session('error')) {
        $toasts->push(['type' => 'error', 'message' => session('error')]);
    }

    if (session('info')) {
        $toasts->push(['type' => 'info', 'message' => session('info')]);
    }

    if (session('status')) {
        $status = session('status');
        $type = in_array($status, ['verification-link-sent', 'password-updated'], true) ? 'success' : 'info';
        $message = match ($status) {
            'verification-link-sent' => __('A new verification link has been sent to your email address.'),
            'password-updated' => __('Password updated successfully.'),
            default => is_string($status) ? $status : (string) $status,
        };
        $toasts->push(['type' => $type, 'message' => $message]);
    }

    if (isset($errors) && $errors->any()) {
        foreach ($errors->getMessages() as $fieldMessages) {
            $message = is_array($fieldMessages) ? ($fieldMessages[0] ?? null) : $fieldMessages;

            if (filled($message)) {
                $toasts->push(['type' => 'error', 'message' => $message]);
            }
        }
    }
@endphp

@if ($toasts->isNotEmpty())
    <script type="application/json" id="app-flash-toasts">@json($toasts->values())</script>
@endif
