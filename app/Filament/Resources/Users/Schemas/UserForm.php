<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use App\Support\Currencies;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Select::make('role')
                    ->options(collect(UserRole::cases())->mapWithKeys(fn (UserRole $role) => [$role->value => $role->label()]))
                    ->required()
                    ->default(UserRole::User->value),
                Toggle::make('is_approved')
                    ->label('Approved')
                    ->visible(fn (?string $operation, $record) => $record?->role !== UserRole::Admin),
                Toggle::make('ai_scan_enabled')
                    ->label('AI Scan enabled')
                    ->helperText('Allow this user to upload receipts and use AI bill scanning in the user panel.')
                    ->default(true)
                    ->visible(fn (?string $operation, $record) => ($record?->role ?? UserRole::User) !== UserRole::Admin),
                DateTimePicker::make('approved_at')
                    ->visible(fn (?string $operation, $record) => $record?->role !== UserRole::Admin),
                TextInput::make('membership_fee')
                    ->label('Membership fee')
                    ->numeric()
                    ->prefix(fn (Get $get, $record): string => Currencies::symbol($get('currency') ?? $record?->currency))
                    ->minValue(0)
                    ->visible(fn (?string $operation, $record) => $record?->role !== UserRole::Admin),
                DateTimePicker::make('membership_expires_at')
                    ->label('Membership expires')
                    ->visible(fn (?string $operation, $record) => $record?->role !== UserRole::Admin),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
            ]);
    }
}
