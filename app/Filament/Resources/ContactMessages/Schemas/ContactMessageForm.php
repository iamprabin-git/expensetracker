<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Incoming message')
                    ->schema([
                        TextInput::make('name')->disabled(),
                        TextInput::make('email')->disabled(),
                        TextInput::make('subject')->disabled(),
                        Textarea::make('message')->disabled()->rows(6)->columnSpanFull(),
                        Placeholder::make('received_at')
                            ->label('Received')
                            ->content(fn ($record) => $record?->created_at?->format('M j, Y g:i A') ?? '—'),
                    ])
                    ->columns(2),
                Section::make('Your reply')
                    ->schema([
                        Textarea::make('admin_reply')
                            ->label('Reply sent to visitor')
                            ->rows(6)
                            ->columnSpanFull()
                            ->disabled(fn ($record) => $record?->hasReply()),
                        Placeholder::make('replied_at_display')
                            ->label('Replied at')
                            ->content(fn ($record) => $record?->replied_at?->format('M j, Y g:i A') ?? 'Not replied yet'),
                    ]),
                Toggle::make('is_read')->label('Mark as read'),
            ]);
    }
}
