<?php

namespace Nox\Framework\Admin\Filament\Pages;

use Filament\Facades\Filament;
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
        $this->form->fill();
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
}
