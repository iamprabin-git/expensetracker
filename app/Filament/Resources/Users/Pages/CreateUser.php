<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\UserRole;
use App\Filament\Resources\Users\UserResource;
use App\Services\UserRegistrationService;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected ?string $plainPassword = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->plainPassword = $data['password'] ?? null;

        return $data;
    }

    protected function afterCreate(): void
    {
        $user = $this->record;

        if (
            $user->role === UserRole::User
            && ! $user->is_approved
            && filled($this->plainPassword)
        ) {
            app(UserRegistrationService::class)->sendRegistrationEmails($user, $this->plainPassword);
        }
    }
}
