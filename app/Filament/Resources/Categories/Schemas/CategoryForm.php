<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Enums\CategoryType;
use App\Enums\UserRole;
use App\Models\User;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->options(collect(CategoryType::cases())->mapWithKeys(fn (CategoryType $type) => [$type->value => $type->label()]))
                    ->required(),
                ColorPicker::make('color')
                    ->default('#6366f1'),
                Select::make('user_id')
                    ->label('Owner (optional)')
                    ->options(User::query()->where('role', UserRole::User)->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
            ]);
    }
}
