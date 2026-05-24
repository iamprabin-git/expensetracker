<?php

namespace App\Filament\Resources\Categories\Tables;

use App\Support\CategoryIcons;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('icon')
                    ->label('Icon')
                    ->formatStateUsing(fn (?string $state) => CategoryIcons::label($state))
                    ->badge()
                    ->color('gray'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label() ?? $state)
                    ->color(fn ($state) => $state?->filamentColor() ?? 'gray'),
                TextColumn::make('transactions_count')
                    ->counts('transactions')
                    ->label('Uses'),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
