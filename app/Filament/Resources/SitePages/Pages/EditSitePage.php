<?php

namespace App\Filament\Resources\SitePages\Pages;

use App\Filament\Resources\SitePages\SitePageResource;
use App\Models\SitePage;
use App\Services\SiteContentService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\ValidationException;

class EditSitePage extends EditRecord
{
    protected static string $resource = SitePageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view')
                ->label('View page')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->url(fn (): string => $this->getRecord()->publicUrl())
                ->openUrlInNewTab()
                ->visible(fn (): bool => (bool) $this->getRecord()->is_published),
            DeleteAction::make()
                ->visible(fn (): bool => $this->getRecord()->isCustom()),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->getRecord()->isCustom()) {
            $data['slug'] = SitePage::normalizeSlug(
                filled($data['slug'] ?? null)
                    ? $data['slug']
                    : ($data['label'] ?? $this->getRecord()->label)
            );

            if ($data['slug'] === '') {
                throw ValidationException::withMessages([
                    'label' => 'Page name is required to generate a URL slug.',
                ]);
            }

            if (SitePage::isReservedSlug($data['slug']) && $data['slug'] !== $this->getRecord()->slug) {
                throw ValidationException::withMessages([
                    'slug' => 'This slug is reserved or already used by the app.',
                ]);
            }
        }

        return $data;
    }

    protected function afterSave(): void
    {
        app(SiteContentService::class)->flush();
    }

    protected function afterDelete(): void
    {
        app(SiteContentService::class)->flush();
    }
}
