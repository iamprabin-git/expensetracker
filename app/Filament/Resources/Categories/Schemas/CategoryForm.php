<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Enums\CategoryType;
use App\Support\CategoryIcons;
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
                    ->required()
                    ->live(),
                Select::make('icon')
                    ->label('Icon')
                    ->options(CategoryIcons::options())
                    ->default(CategoryIcons::DEFAULT)
                    ->required()
                    ->searchable(),
            ]);
    }
}
