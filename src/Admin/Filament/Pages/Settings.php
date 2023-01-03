<?php

namespace Nox\Framework\Admin\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Nox\Framework\Updater\Jobs\NoxUpdateJob;

class Settings extends Page
{
    protected static string $view = 'nox::filament.pages.settings';

    protected static ?string $slug = 'system/settings';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 100;

    protected ?string $availableUpdateVersion = null;

    public function mount(): void
    {
        $this->form->fill();

        $this->availableUpdateVersion = Cache::get('nox.updater.available');
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
