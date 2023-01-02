<?php

namespace Nox\Framework\Updater\Jobs;

use Composer\InstalledVersions;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Artisan;
use Nox\Framework\Auth\Models\User;
use Nox\Framework\Support\Composer;

class NoxUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(
        protected User $user,
        protected string $version
    )
    {
    }

    public function handle(Composer $composer): void
    {
        $status = $composer->update('nox-php/framework');

        if ($status === 0) {
            Notification::make()
                ->success()
                ->title('Nox has successfully updated')
                ->body('Nox ' . $this->version . ' has been successfully installed')
                ->sendToDatabase($this->user);
        } else {
            $currentVersion = InstalledVersions::getVersion('nox-php/framework');

            Notification::make()
                ->danger()
                ->title('Nox has unsuccessfully updated')
                ->body('Nox ' . $this->version . ' has failed to install, reverting back to ' . $currentVersion)
                ->sendToDatabase($this->user);
        }
    }
}
