<?php

namespace Nox\Framework\Updater\Jobs;

use Composer\InstalledVersions;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\URL;
use Nox\Framework\Auth\Models\User;
use Nox\Framework\Support\Composer;

class NoxUpdateJob implements ShouldQueue, ShouldBeUnique
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
                ->actions([
                    Action::make('update-nox-retry')
                        ->button()
                        ->label('Retry')
                        ->url(URL::signedRoute('nox.updater', ['version' => $this->version]))
                ])
                ->sendToDatabase($this->user);
        }
    }
}
