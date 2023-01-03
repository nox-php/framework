<?php

namespace Nox\Framework\Updater\Jobs;

use Composer\InstalledVersions;
use Exception;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\URL;
use Nox\Framework\Admin\Filament\Resources\ActivityResource;
use Nox\Framework\Auth\Models\User;
use Nox\Framework\Support\Composer;

class NoxUpdateJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected User $user;

    protected string $version;

    public function __construct(User $user, string $version)
    {
        $this->user = $user->withoutRelations();
        $this->version = $version;
    }

    public function handle(Composer $composer): void
    {
        $currentVersion = InstalledVersions::getVersion('nox-php/framework');

        rescue(function () use ($composer, $currentVersion) {
            $this->update($composer, $currentVersion);
        }, function (Exception $e) use ($currentVersion) {
            $this->handleError($e, $currentVersion);
        });
    }

    protected function update(Composer $composer, string $currentVersion): void
    {
        $status = $composer->update('nox-php/framework:' . $this->version);

        $log = activity()
            ->by($this->user)
            ->event('nox.update')
            ->withProperty('status', $status)
            ->log($composer->getOutput()?->fetch() ?? '-');

        if ($status !== 0) {
            Notification::make()
                ->danger()
                ->title('Nox has unsuccessfully updated')
                ->body('Nox ' . $this->version . ' has failed to install, reverting back to ' . $currentVersion)
                ->actions([
                    Action::make('update-nox-retry')
                        ->button()
                        ->label('Retry')
                        ->url(URL::signedRoute('nox.updater', ['version' => $this->version])),
                    Action::make('view-log')
                        ->button()
                        ->label('View log')
                        ->color('secondary')
                        ->url(ActivityResource::getUrl('view', ['record' => $log->id]))
                ])
                ->sendToDatabase($this->user);

            return;
        }

        Notification::make()
            ->success()
            ->title('Nox has successfully updated')
            ->body('Nox ' . $this->version . ' has been successfully installed')
            ->actions([
                Action::make('view-log')
                    ->button()
                    ->label('View log')
                    ->color('secondary')
                    ->url(ActivityResource::getUrl('view', ['record' => $log->id]))
            ])
            ->sendToDatabase($this->user);

//        Artisan::call('vendor:publish', [
//            '--tag' => 'laravel-assets',
//            '--force' => true,
//        ]);
//
//        Artisan::call('package:discover');
    }

    protected function handleError(Exception $e, string $currentVersion): void
    {
        activity()
            ->by($this->user)
            ->event('nox.update')
            ->log((string)$e);

        Notification::make()
            ->danger()
            ->title('Nox has unsuccessfully updated')
            ->body('Nox ' . $this->version . ' has failed to install, reverting back to ' . $currentVersion)
            ->actions([
                Action::make('update-nox-retry')
                    ->button()
                    ->label('Retry')
                    ->url(URL::signedRoute('nox.updater', ['version' => $this->version])),
            ])
            ->sendToDatabase($this->user);
    }
}
