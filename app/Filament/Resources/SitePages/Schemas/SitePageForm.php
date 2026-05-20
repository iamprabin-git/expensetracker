<?php

namespace App\Filament\Resources\SitePages\Schemas;

use App\Models\SitePage;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
class SitePageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Page')
                    ->tabs([
                        Tab::make('General')
                            ->schema([
                                TextInput::make('label')
                                    ->label('Page name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true),
                                TextInput::make('slug')
                                    ->label('URL slug')
                                    ->maxLength(120)
                                    ->nullable()
                                    ->placeholder(fn (Get $get): ?string => filled($get('label'))
                                        ? SitePage::normalizeSlug($get('label'))
                                        : null)
                                    ->unique(ignoreRecord: true)
                                    ->rules(fn (?SitePage $record): array => [
                                        'nullable',
                                        'max:120',
                                        'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                                        function (string $attribute, mixed $value, \Closure $fail) use ($record): void {
                                            if (blank($value)) {
                                                return;
                                            }

                                            $slug = SitePage::normalizeSlug((string) $value);

                                            if ($record?->slug === $slug) {
                                                return;
                                            }

                                            if (SitePage::isReservedSlug($slug)) {
                                                $fail('This slug is reserved or already used by the app.');
                                            }
                                        },
                                    ])
                                    ->helperText(function (Get $get, ?SitePage $record): string {
                                        if ($record) {
                                            return 'Public URL: '.$record->publicPath();
                                        }

                                        $preview = filled($get('label'))
                                            ? SitePage::normalizeSlug($get('label'))
                                            : 'your-slug';

                                        return "Leave blank to auto-generate from the page name (e.g. /pages/{$preview}).";
                                    })
                                    ->disabled(fn (?SitePage $record): bool => $record?->isSystem() ?? false)
                                    ->dehydrated(),
                                TextInput::make('title')
                                    ->label('Browser title')
                                    ->maxLength(255),
                                Textarea::make('meta_description')
                                    ->rows(2)
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                                Toggle::make('is_published')
                                    ->label('Published on website')
                                    ->default(true),
                            ])
                            ->columns(2),
                        Tab::make('Hero')
                            ->schema([
                                TextInput::make('hero_badge')->maxLength(120),
                                TextInput::make('hero_title')->maxLength(255)->columnSpanFull(),
                                Textarea::make('hero_lead')->rows(3)->columnSpanFull(),
                                FileUpload::make('hero_image')
                                    ->label('Hero image')
                                    ->image()
                                    ->disk('public')
                                    ->directory('site-content')
                                    ->visibility('public')
                                    ->maxSize(4096)
                                    ->imageEditor()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                        Tab::make('Sections')
                            ->schema([
                                Repeater::make('sections')
                                    ->label('Page sections')
                                    ->schema([
                                        Select::make('type')
                                            ->options([
                                                'section_header' => 'Section heading',
                                                'stats' => 'Stats row',
                                                'feature_cards' => 'Feature cards',
                                                'steps' => 'Numbered steps',
                                                'values' => 'Values list',
                                                'pricing' => 'Pricing plans',
                                                'faq' => 'FAQ accordion',
                                                'contact_info' => 'Contact info list',
                                                'hero_bullets' => 'Hero bullet list',
                                                'image_text' => 'Text with image',
                                                'reviews' => 'Reviews block (title only)',
                                                'cta' => 'Call to action',
                                            ])
                                            ->required()
                                            ->native(false),
                                        TextInput::make('title')->maxLength(255),
                                        Textarea::make('subtitle')->rows(2)->columnSpanFull(),
                                        FileUpload::make('image')
                                            ->image()
                                            ->disk('public')
                                            ->directory('site-content')
                                            ->visibility('public')
                                            ->maxSize(4096)
                                            ->columnSpanFull(),
                                        Repeater::make('items')
                                            ->schema([
                                                TextInput::make('title')->maxLength(255),
                                                Textarea::make('text')->rows(3),
                                                TextInput::make('value')->label('Stat value'),
                                                TextInput::make('label')->label('Stat label'),
                                                TextInput::make('icon')->label('SVG path')->columnSpanFull(),
                                                FileUpload::make('image')
                                                    ->image()
                                                    ->disk('public')
                                                    ->directory('site-content')
                                                    ->visibility('public')
                                                    ->maxSize(4096),
                                                TextInput::make('price'),
                                                TextInput::make('period'),
                                                TextInput::make('badge'),
                                                Toggle::make('featured'),
                                                Textarea::make('features')
                                                    ->helperText('One feature per line.')
                                                    ->rows(4)
                                                    ->columnSpanFull(),
                                                TextInput::make('link_label'),
                                                TextInput::make('link_url'),
                                            ])
                                            ->columns(2)
                                            ->collapsible()
                                            ->defaultItems(0)
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible()
                                    ->cloneable()
                                    ->reorderable()
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Legal body')
                            ->schema([
                                RichEditor::make('body_html')
                                    ->helperText('Optional rich content. Can be used alone or together with sections.')
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Page extras')
                            ->schema([
                                TextInput::make('extras.hero_note')
                                    ->visible(fn (?SitePage $record) => $record?->slug === 'home'),
                                TextInput::make('extras.primary_cta_label')
                                    ->visible(fn (?SitePage $record) => $record?->slug === 'home'),
                                TextInput::make('extras.primary_cta_url')
                                    ->visible(fn (?SitePage $record) => $record?->slug === 'home'),
                                TextInput::make('extras.secondary_cta_label')
                                    ->visible(fn (?SitePage $record) => $record?->slug === 'home'),
                                TextInput::make('extras.secondary_cta_url')
                                    ->visible(fn (?SitePage $record) => $record?->slug === 'home'),
                                TextInput::make('extras.sidebar_title')
                                    ->visible(fn (?SitePage $record) => $record?->slug === 'contact'),
                                TextInput::make('extras.form_title')
                                    ->visible(fn (?SitePage $record) => $record?->slug === 'contact'),
                                Textarea::make('extras.success_message')
                                    ->rows(2)
                                    ->visible(fn (?SitePage $record) => $record?->slug === 'contact'),
                                Repeater::make('extras.custom')
                                    ->label('Custom fields (key / value)')
                                    ->schema([
                                        TextInput::make('key')->required(),
                                        Textarea::make('value')->rows(2),
                                    ])
                                    ->columns(2)
                                    ->visible(fn (?SitePage $record) => $record?->isCustom() ?? true)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
