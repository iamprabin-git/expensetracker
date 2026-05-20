<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class AdminEditProfile extends BaseEditProfile
{
    protected static ?string $title = 'My profile';

    protected static ?string $navigationLabel = 'Profile';

    protected static ?string $slug = 'profile';

    public static function getNavigationIcon(): string|\BackedEnum|\Illuminate\Contracts\Support\Htmlable|null
    {
        return Heroicon::OutlinedUserCircle;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function getNavigationSort(): ?int
    {
        return 100;
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('removeAvatar')
                ->label('Remove photo')
                ->icon(Heroicon::OutlinedTrash)
                ->color('danger')
                ->outlined()
                ->visible(fn (): bool => filled($this->getUser()->avatar_path))
                ->requiresConfirmation()
                ->action(function (): void {
                    $user = $this->getUser();

                    if ($user->avatar_path) {
                        Storage::disk('public')->delete($user->avatar_path);
                        $user->update(['avatar_path' => null]);
                    }

                    $this->fillForm();

                    Notification::make()
                        ->title('Profile photo removed')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile photo')
                    ->description('Upload a square image (JPEG, PNG, or WebP, max 2 MB).')
                    ->schema([
                        $this->getAvatarFormComponent(),
                    ])
                    ->columns(1),
                Section::make('Contact details')
                    ->description('Your name and how we reach you.')
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPhoneFormComponent(),
                    ])
                    ->columns(2),
                Section::make('Password')
                    ->description('Leave blank to keep your current password.')
                    ->schema([
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getCurrentPasswordFormComponent(),
                    ])
                    ->columns(1),
            ]);
    }

    protected function getAvatarFormComponent(): Component
    {
        return FileUpload::make('avatar_path')
            ->label('Profile image')
            ->image()
            ->avatar()
            ->disk('public')
            ->directory('avatars')
            ->visibility('public')
            ->maxSize(2048)
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->imageEditor()
            ->nullable();
    }

    protected function getPhoneFormComponent(): Component
    {
        return TextInput::make('phone')
            ->label('Phone')
            ->tel()
            ->maxLength(30)
            ->placeholder('+1 555 000 0000')
            ->columnSpanFull();
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('New password')
            ->validationAttribute('password')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->rule(Password::defaults())
            ->autocomplete('new-password')
            ->dehydrated(fn ($state): bool => filled($state))
            ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
            ->live(debounce: 500)
            ->same('passwordConfirmation');
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label('Confirm new password')
            ->validationAttribute('password confirmation')
            ->password()
            ->autocomplete('new-password')
            ->revealable(filament()->arePasswordsRevealable())
            ->required(fn (Get $get): bool => filled($get('password')))
            ->visible(fn (Get $get): bool => filled($get('password')))
            ->dehydrated(false);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = $this->getUser();

        if (array_key_exists('avatar_path', $data) && $user->avatar_path && $data['avatar_path'] !== $user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        unset($data['passwordConfirmation'], $data['currentPassword']);

        return $data;
    }
}
