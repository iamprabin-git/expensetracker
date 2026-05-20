<?php

namespace App\Filament\Resources\SitePages\Pages;

use App\Filament\Resources\SitePages\SitePageResource;
use App\Models\SitePage;
use App\Services\SiteContentService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateSitePage extends CreateRecord
{
    protected static string $resource = SitePageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['label'] = trim($data['label'] ?? '');
        $data['slug'] = SitePage::normalizeSlug($data['slug'] ?? $data['label']);

        if ($data['label'] === '' || $data['slug'] === '') {
            throw ValidationException::withMessages([
                'label' => 'Page name is required.',
            ]);
        }

        if (SitePage::isReservedSlug($data['slug'])) {
            throw ValidationException::withMessages([
                'slug' => 'This URL slug is reserved. Choose a different slug.',
            ]);
        }

        if (empty($data['title'])) {
            $data['title'] = $data['label'];
        }

        $data['is_published'] = $data['is_published'] ?? true;
        $data['sections'] = $data['sections'] ?? [];
        $data['extras'] = $data['extras'] ?? [];

        return $data;
    }

    protected function afterCreate(): void
    {
        app(SiteContentService::class)->flush();

        Notification::make()
            ->title('Page created')
            ->body('Live URL: '.$this->record->publicUrl())
            ->success()
            ->send();
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return null;
    }
}
