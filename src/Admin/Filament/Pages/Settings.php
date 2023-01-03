<?php

namespace Nox\Framework\Admin\Filament\Pages;

use Closure;
use Filament\Facades\Filament;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Nox\Framework\Updater\Jobs\NoxCheckUpdateJob;
use Nox\Framework\Updater\Jobs\NoxUpdateJob;

class Settings extends Page
{
    protected static string $view = 'nox::filament.pages.settings';

    protected static ?string $slug = 'system/settings';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 100;

    public ?string $availableUpdateVersion = null;

    public function mount(): void
    {
        $databaseConfig = collect(config('database.connections.' . config('database.default'), []))
            ->only([
                'driver',
                'host',
                'port',
                'database',
                'username'
            ])
            ->mapWithKeys(static fn($value, $key): array => [
                'database_' . $key => $value
            ])
            ->all();

        $state = [
            'site_name' => config('app.name'),
            'site_url' => config('app.url'),
            'site_environment' => config('app.env'),
            'site_debug' => config('app.debug'),

            ...$databaseConfig,

            'discord_client_id' => config('nox.auth.discord.client_id'),
        ];

        $this->form->fill($state);
    }

    protected function getViewData(): array
    {
        $this->availableUpdateVersion = Cache::get('nox.updater.available');

        return parent::getViewData();
    }

    public function installUpdate(): void
    {
        if ($this->availableUpdateVersion === null) {
            return;
        }

        NoxUpdateJob::dispatch(Filament::auth()->user(), $this->availableUpdateVersion);

        Notification::make()
            ->success()
            ->title('Nox is updating in the background')
            ->body('You will be notified once it has finished')
            ->send();
    }

    public function checkUpdate(): void
    {
        NoxCheckUpdateJob::dispatch(Filament::auth()->user());

        Notification::make()
            ->success()
            ->title('Checking for Nox updates in the background')
            ->body('You will be notified if an update is available')
            ->send();
    }

    protected function getActions(): array
    {
        return [
            Action::make('check-nox-update')
                ->label('Check for updates')
                ->button()
                ->action('checkUpdate')
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Tabs::make('Settings')
                ->disableLabel()
                ->tabs([
                    Tabs\Tab::make('Site')
                        ->schema([
                            Fieldset::make('Global')
                                ->schema([
                                    TextInput::make('site_name')
                                        ->label('Site name')
                                        ->required()
                                        ->maxLength(255),
                                    TextInput::make('site_url')
                                        ->required()
                                        ->maxLength(255)
                                ]),
                            Fieldset::make('Debugging')
                                ->schema([
                                    Select::make('site_environment')
                                        ->label('Environment')
                                        ->required()
                                        ->options([
                                            'production' => 'Production',
                                            'testing' => 'Testing',
                                            'local' => 'Local'
                                        ]),
                                    Toggle::make('site_debug')
                                        ->label('Enable debug mode')
                                        ->helperText('This should never be enabled in production')
                                ])
                        ]),
                    Tabs\Tab::make('Database')
                        ->schema([
                            Grid::make()
                                ->schema([
                                    Select::make('database_driver')
                                        ->label('Driver')
                                        ->reactive()
                                        ->required()
                                        ->options([
                                            'mysql' => 'mysql',
                                            'pgsql' => 'pgsql',
                                            'sqlsrv' => 'sqlsrv',
                                            'sqlite' => 'sqlite'
                                        ]),
                                    TextInput::make('database_host')
                                        ->label('Host')
                                        ->required(static fn(Closure $get): bool => $get('database_driver') !== 'sqlite')
                                        ->hidden(static fn(Closure $get): bool => $get('database_driver') === 'sqlite'),
                                    TextInput::make('database_port')
                                        ->label('Port')
                                        ->integer()
                                        ->minValue(1)
                                        ->required(static fn(Closure $get): bool => $get('database_driver') !== 'sqlite')
                                        ->hidden(static fn(Closure $get): bool => $get('database_driver') === 'sqlite'),
                                    TextInput::make('database_database')
                                        ->label('Database')
                                        ->required(),
                                    TextInput::make('database_username')
                                        ->label('Username')
                                        ->required(static fn(Closure $get): bool => $get('database_driver') !== 'sqlite')
                                        ->hidden(static fn(Closure $get): bool => $get('database_driver') === 'sqlite'),
                                    TextInput::make('database_password')
                                        ->label('Password')
                                        ->password()
                                        ->hidden(static fn(Closure $get): bool => $get('database_driver') === 'sqlite'),
                                ])
                        ]),
                    Tabs\Tab::make('Discord')
                        ->schema([
                            Grid::make()
                                ->schema([
                                    TextInput::make('discord_client_id')
                                        ->label('Client ID')
                                        ->required(),
                                    TextInput::make('discord_client_secret')
                                        ->label('Client secret')
                                        ->required(static fn(): bool => config('nox.auth.discord.client_secret') === null)
                                        ->password()
                                ])
                        ]),
                    Tabs\Tab::make('Email')
                        ->schema([
                            Grid::make()
                                ->schema([
                                    Select::make('email_driver')
                                        ->label('Driver')
                                        ->reactive()
                                        ->required()
                                        ->options([
                                            'smtp' => 'smtp',
                                            'sendmail' => 'sendmail'
                                        ]),
                                    TextInput::make('path')
                                        ->label('Path')
                                        ->default('/usr/sbin/sendmail -bs -i')
                                        ->required(static fn(Closure $get): bool => $get('email_driver') === 'sendmail')
                                        ->hidden(static fn(Closure $get): bool => $get('email_driver') !== 'sendmail'),
                                    Select::make('email_host')
                                        ->label('Host')
                                        ->required(static fn(Closure $get): bool => $get('email_driver') !== 'sendmail')
                                        ->hidden(static fn(Closure $get): bool => $get('email_driver') === 'sendmail'),
                                    TextInput::make('email_port')
                                        ->label('Port')
                                        ->integer()
                                        ->minValue(1)
                                        ->required(static fn(Closure $get): bool => $get('email_driver') !== 'sendmail')
                                        ->hidden(static fn(Closure $get): bool => $get('email_driver') === 'sendmail'),
                                    TextInput::make('email_username')
                                        ->label('Username')
                                        ->required(static fn(Closure $get): bool => $get('email_driver') !== 'sendmail')
                                        ->hidden(static fn(Closure $get): bool => $get('email_driver') === 'sendmail'),
                                    TextInput::make('email_password')
                                        ->label('Password')
                                        ->password()
                                        ->hidden(static fn(Closure $get): bool => $get('email_driver') === 'sendmail'),
                                    TextInput::make('email_encryption')
                                        ->label('Encryption')
                                        ->required(static fn(Closure $get): bool => $get('email_driver') !== 'sendmail')
                                        ->hidden(static fn(Closure $get): bool => $get('email_driver') === 'sendmail')
                                        ->default('tls')
                                ])
                        ])
                ])
        ];
    }
}
