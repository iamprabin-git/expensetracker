<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Enums\TransactionType;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('User')
                    ->options(User::query()->where('role', UserRole::User)->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('type')
                    ->options(collect(TransactionType::cases())->mapWithKeys(fn (TransactionType $type) => [$type->value => $type->label()]))
                    ->required(),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('amount')
                    ->numeric()
                    ->prefix('$')
                    ->required()
                    ->minValue(0.01),
                Select::make('category_id')
                    ->label('Category')
                    ->options(fn () => Category::query()->system()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                DatePicker::make('transaction_date')
                    ->required()
                    ->default(now()),
                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
