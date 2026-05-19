<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('display_name')
                    ->required()
                    ->maxLength(80)
                    ->helperText('Public name shown on the website only.'),
                Select::make('rating')
                    ->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5'])
                    ->required(),
                Textarea::make('content')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),
                Toggle::make('is_approved')
                    ->label('Approved for website'),
            ]);
    }
}
