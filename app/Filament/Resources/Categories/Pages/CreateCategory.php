<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Enums\CategoryType;
use App\Filament\Resources\Categories\CategoryResource;
use App\Support\CategoryIcons;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = null;
        $data['icon'] = CategoryIcons::normalize(
            $data['icon'] ?? CategoryIcons::defaultForType(
                CategoryType::tryFrom($data['type'] ?? '') ?? CategoryType::Expense
            )
        );

        return $data;
    }
}
