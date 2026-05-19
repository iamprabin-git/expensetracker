<?php

namespace App\Filament\Resources\Reviews\Tables;

use App\Models\Review;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_approved')
                    ->label('Live')
                    ->boolean(),
                TextColumn::make('display_name')
                    ->label('Public name')
                    ->searchable(),
                TextColumn::make('rating')
                    ->badge()
                    ->color(fn (int $state) => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        default => 'danger',
                    }),
                TextColumn::make('content')
                    ->limit(60),
                TextColumn::make('source')
                    ->label('Source')
                    ->badge()
                    ->getStateUsing(fn (Review $record) => $record->user_id ? 'Member' : 'Website')
                    ->color(fn (string $state) => $state === 'Member' ? 'info' : 'gray'),
                TextColumn::make('user.email')
                    ->label('Account email')
                    ->placeholder('Guest submission')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_approved')->label('Website visibility'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Review $record) => ! $record->is_approved)
                    ->requiresConfirmation()
                    ->action(function (Review $record): void {
                        $record->approve();
                        Notification::make()->title('Review published on website')->success()->send();
                    }),
                Action::make('unpublish')
                    ->label('Unpublish')
                    ->icon('heroicon-o-eye-slash')
                    ->color('warning')
                    ->visible(fn (Review $record) => $record->is_approved)
                    ->requiresConfirmation()
                    ->action(function (Review $record): void {
                        $record->reject();
                        Notification::make()->title('Review hidden from website')->warning()->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
