<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_read')
                    ->label('Read')
                    ->boolean(),
                IconColumn::make('replied_at')
                    ->label('Replied')
                    ->boolean()
                    ->getStateUsing(fn (ContactMessage $record) => $record->hasReply()),
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable()->copyable(),
                TextColumn::make('subject')->searchable()->limit(40),
                TextColumn::make('message')->limit(50)->toggleable(),
                TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('replied_at')
                    ->label('Replied at')
                    ->dateTime()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_read')->label('Read status'),
                TernaryFilter::make('needs_reply')
                    ->label('Needs reply')
                    ->queries(
                        true: fn ($query) => $query->whereNull('admin_reply'),
                        false: fn ($query) => $query->whereNotNull('admin_reply'),
                    ),
            ])
            ->recordActions([
                Action::make('reply')
                    ->label('Reply')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->visible(fn (ContactMessage $record) => ! $record->hasReply())
                    ->form([
                        Textarea::make('admin_reply')
                            ->label('Your reply')
                            ->required()
                            ->rows(6)
                            ->placeholder('Type your reply — it will be emailed to the sender.'),
                    ])
                    ->action(function (ContactMessage $record, array $data): void {
                        $record->sendReply($data['admin_reply']);

                        Notification::make()
                            ->title('Reply sent')
                            ->body("Email sent to {$record->email}")
                            ->success()
                            ->send();
                    }),
                Action::make('markRead')
                    ->label('Mark read')
                    ->icon('heroicon-o-envelope-open')
                    ->visible(fn (ContactMessage $record) => ! $record->is_read)
                    ->action(function (ContactMessage $record): void {
                        $record->markAsRead();
                        Notification::make()->title('Marked as read')->success()->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
