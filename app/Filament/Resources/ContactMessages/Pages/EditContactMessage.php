<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditContactMessage extends EditRecord
{
    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reply')
                ->label('Reply by email')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->visible(fn (ContactMessage $record) => ! $record->hasReply())
                ->form([
                    Textarea::make('admin_reply')
                        ->label('Your reply')
                        ->required()
                        ->rows(8),
                ])
                ->action(function (array $data): void {
                    /** @var ContactMessage $record */
                    $record = $this->record;
                    $record->sendReply($data['admin_reply']);

                    Notification::make()
                        ->title('Reply sent')
                        ->body("Email sent to {$record->email}")
                        ->success()
                        ->send();

                    $this->refreshFormData(['admin_reply', 'replied_at', 'is_read', 'read_at']);
                }),
            DeleteAction::make(),
        ];
    }
}
