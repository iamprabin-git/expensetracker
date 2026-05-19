<?php

namespace App\Notifications\Concerns;

trait FormatsDatabaseNotification
{
    protected function databasePayload(
        string $category,
        string $title,
        string $message,
        ?string $actionUrl = null,
        ?string $actionLabel = null,
        array $extra = [],
    ): array {
        return array_merge([
            'category' => $category,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'action_label' => $actionLabel ?? 'View',
        ], $extra);
    }
}
