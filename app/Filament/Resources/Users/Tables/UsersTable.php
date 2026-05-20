<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\UserRole;
use App\Models\User;
use App\Support\Currencies;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->where('role', UserRole::User))
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(),
                IconColumn::make('is_approved')
                    ->label('Approved')
                    ->boolean(),
                IconColumn::make('ai_scan_enabled')
                    ->label('AI Scan')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('currency')
                    ->label('Currency')
                    ->formatStateUsing(fn (?string $state): string => $state
                        ? Currencies::formatLabel($state, Currencies::all()[$state] ?? ['name' => $state, 'symbol' => ''])
                        : '—')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('membership_fee')
                    ->label('Membership fee')
                    ->formatStateUsing(fn ($state, User $record): string => $state !== null
                        ? Currencies::symbol($record->currency).number_format((float) $state, 2)
                        : '—')
                    ->placeholder('—'),
                TextColumn::make('membership_expires_at')
                    ->label('Expires')
                    ->dateTime()
                    ->placeholder('—')
                    ->color(fn (User $record) => $record->membership_expires_at?->isPast() ? 'danger' : null),
                TextColumn::make('approved_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_approved')->label('Approval status'),
                TernaryFilter::make('ai_scan_enabled')->label('AI Scan access'),
                SelectFilter::make('currency')
                    ->label('Currency')
                    ->options(Currencies::selectOptions()),
                SelectFilter::make('membership_status')
                    ->label('Membership')
                    ->options([
                        'active' => 'Active',
                        'expired' => 'Expired',
                        'none' => 'No expiry set',
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === 'active') {
                            $query->where(function ($q) {
                                $q->whereNull('membership_expires_at')
                                    ->orWhere('membership_expires_at', '>', now());
                            });
                        } elseif ($data['value'] === 'expired') {
                            $query->where('membership_expires_at', '<=', now());
                        } elseif ($data['value'] === 'none') {
                            $query->whereNull('membership_expires_at');
                        }
                    }),
            ])
            ->recordActions([
                Action::make('toggleAiScan')
                    ->label(fn (User $record): string => $record->ai_scan_enabled ? 'Disable AI Scan' : 'Enable AI Scan')
                    ->icon(fn (User $record): string => $record->ai_scan_enabled ? 'heroicon-o-no-symbol' : 'heroicon-o-sparkles')
                    ->color(fn (User $record): string => $record->ai_scan_enabled ? 'warning' : 'success')
                    ->action(function (User $record): void {
                        $record->update(['ai_scan_enabled' => ! $record->ai_scan_enabled]);

                        Notification::make()
                            ->title($record->ai_scan_enabled ? 'AI Scan enabled' : 'AI Scan disabled')
                            ->success()
                            ->send();
                    }),
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (User $record) => ! $record->is_approved)
                    ->form([
                        TextInput::make('membership_fee')
                            ->label('Membership fee')
                            ->numeric()
                            ->prefix('$')
                            ->default(9.00),
                        DateTimePicker::make('membership_expires_at')
                            ->label('Expires at')
                            ->default(now()->addYear()),
                    ])
                    ->action(function (User $record, array $data): void {
                        $expiresAt = filled($data['membership_expires_at'] ?? null)
                            ? Carbon::parse($data['membership_expires_at'])
                            : null;

                        $record->approve(
                            isset($data['membership_fee']) ? (float) $data['membership_fee'] : null,
                            $expiresAt,
                        );

                        Notification::make()
                            ->title('User approved')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Revoke')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (User $record) => $record->is_approved)
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        $record->reject();

                        Notification::make()
                            ->title('Approval revoked')
                            ->warning()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('enableAiScan')
                        ->label('Enable AI Scan')
                        ->icon('heroicon-o-sparkles')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['ai_scan_enabled' => true]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('disableAiScan')
                        ->label('Disable AI Scan')
                        ->icon('heroicon-o-no-symbol')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['ai_scan_enabled' => false]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
