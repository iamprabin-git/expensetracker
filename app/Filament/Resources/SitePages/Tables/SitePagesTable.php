<?php

namespace App\Filament\Resources\SitePages\Tables;

use App\Models\SitePage;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SitePagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->badge()
                    ->color(fn (SitePage $record): string => $record->isCustom() ? 'success' : 'gray')
                    ->searchable(),
                TextColumn::make('publicPath')
                    ->label('URL')
                    ->state(fn (SitePage $record): string => $record->publicPath())
                    ->url(fn (SitePage $record): string => $record->publicUrl())
                    ->openUrlInNewTab()
                    ->color('primary'),
                IconColumn::make('is_published')
                    ->boolean()
                    ->label('Live'),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn (SitePage $record): bool => $record->isCustom()),
            ])
            ->defaultSort('label');
    }
}
