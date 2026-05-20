<?php

namespace App\Filament\Pages;

use App\Models\CompanySetting;
use App\Services\CompanySettingService;
use Filament\Actions\Action;
use App\Support\Currencies;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Icons\Heroicon;
use Throwable;
use UnitEnum;

class ManageCompanySettings extends Page
{
    use CanUseDatabaseTransactions;

    protected static string|UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'Company & branding';

    protected static ?int $navigationSort = 0;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $title = 'Company & branding';

    protected static ?string $slug = 'company-settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $data = CompanySetting::current()->toArray();

        if (empty($data['enabled_currencies'])) {
            $data['enabled_currencies'] = array_keys(Currencies::all());
        }

        $this->form->fill($data);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Company')
                    ->tabs([
                        Tab::make('Brand')
                            ->schema([
                                TextInput::make('company_name')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Full legal or product name used in titles and emails.'),
                                TextInput::make('brand_name_primary')
                                    ->label('Brand name (primary)')
                                    ->maxLength(80),
                                TextInput::make('brand_name_accent')
                                    ->label('Brand name (accent)')
                                    ->maxLength(120),
                                Textarea::make('tagline')
                                    ->rows(2)
                                    ->columnSpanFull(),
                                FileUpload::make('logo_path')
                                    ->label('Logo')
                                    ->image()
                                    ->disk('public')
                                    ->directory('company')
                                    ->visibility('public')
                                    ->maxSize(4096)
                                    ->imageEditor(),
                                FileUpload::make('favicon_path')
                                    ->label('Favicon')
                                    ->image()
                                    ->disk('public')
                                    ->directory('company')
                                    ->visibility('public')
                                    ->maxSize(1024),
                            ])
                            ->columns(2),
                        Tab::make('Contact')
                            ->schema([
                                TextInput::make('email')
                                    ->email()
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(50),
                                TextInput::make('support_hours')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                        Tab::make('Address')
                            ->schema([
                                TextInput::make('address_line1')->maxLength(255),
                                TextInput::make('address_line2')->maxLength(255),
                                TextInput::make('city')->maxLength(120),
                                TextInput::make('state')->maxLength(120),
                                TextInput::make('postal_code')->maxLength(20),
                                TextInput::make('country')->maxLength(120),
                            ])
                            ->columns(2),
                        Tab::make('Currency')
                            ->schema([
                                CheckboxList::make('enabled_currencies')
                                    ->label('Available currencies')
                                    ->options(Currencies::checkboxOptions())
                                    ->columns(3)
                                    ->bulkToggleable()
                                    ->default(array_keys(Currencies::all()))
                                    ->helperText('Currencies users can choose in Settings and that admins can assign to accounts.')
                                    ->required()
                                    ->live()
                                    ->columnSpanFull(),
                                Select::make('default_currency')
                                    ->label('Default currency')
                                    ->options(function (Get $get): array {
                                        $codes = $get('enabled_currencies') ?? array_keys(Currencies::all());

                                        return collect(Currencies::all())
                                            ->only($codes)
                                            ->mapWithKeys(fn (array $meta, string $code): array => [
                                                $code => Currencies::formatLabel($code, $meta),
                                            ])
                                            ->all();
                                    })
                                    ->required()
                                    ->native(false)
                                    ->helperText('Applied to new user registrations and as the initial currency when creating users.'),
                            ])
                            ->columns(1),
                        Tab::make('Footer & social')
                            ->schema([
                                Textarea::make('footer_lead')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                TextInput::make('newsletter_title')->maxLength(120),
                                Textarea::make('newsletter_text')
                                    ->rows(2)
                                    ->columnSpanFull(),
                                TextInput::make('copyright_text')
                                    ->helperText('Optional. Leave blank to use company name.')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Repeater::make('social_links')
                                    ->schema([
                                        TextInput::make('title')->label('Aria label')->required(),
                                        TextInput::make('label')->label('Display text')->required(),
                                        TextInput::make('link_url')->label('URL')->required(),
                                    ])
                                    ->columns(3)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Save changes')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ])
                            ->alignment(Alignment::Start)
                            ->key('form-actions'),
                    ]),
            ]);
    }

    public function save(): void
    {
        try {
            $this->beginDatabaseTransaction();

            $data = $this->form->getState();

            $enabled = $data['enabled_currencies'] ?? [];
            $default = $data['default_currency'] ?? Currencies::defaultCode();

            if (! in_array($default, $enabled, true)) {
                $data['default_currency'] = $enabled[0] ?? 'USD';
            }

            CompanySetting::current()->update($data);

            app(CompanySettingService::class)->flush();

            $this->commitDatabaseTransaction();

            Notification::make()
                ->title('Company settings saved')
                ->success()
                ->send();
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction()
                ? $this->rollBackDatabaseTransaction()
                : $this->commitDatabaseTransaction();

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }
    }
}
