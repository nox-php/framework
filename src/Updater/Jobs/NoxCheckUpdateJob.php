<?php

namespace Nox\Framework\Updater\Jobs;

use Composer\InstalledVersions;
use Exception;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Nox\Framework\Auth\Models\User;

class NoxCheckUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected static string $baseUrl = 'https://repo.packagist.org/p2/';

    public function handle(): void
    {
        $installedVersion = InstalledVersions::getVersion('nox-php/framework');
        if ($installedVersion === 'dev-main') {
            return;
        }

        if (!$version = $this->getLatestVersion()) {
            info('Failed to get latest version of nox-php/framework from packagist.');
            return;
        }

        if (version_compare($installedVersion, $version, '>=')) {
            return;
        }

        $users = User::query()
            ->whereCan('view_admin')
            ->lazy();

        $notification = Notification::make()
            ->warning()
            ->title('A new version of Nox is available')
            ->body('Nox ' . $version . ' is ready to be installed')
            ->actions([
                Action::make('update-nox')
                    ->button()
                    ->label('Install')
                    ->url(URL::signedRoute('nox.updater', ['version' => $version]))
            ]);

        foreach ($users as $user) {
            $notification->sendToDatabase($user);
        }
    }

    protected function getLatestVersion(): ?string
    {
        try {
            $response = Http::get(static::$baseUrl . 'nox-php/framework.json');

            $data = json_decode($response->body(), true, 512, JSON_THROW_ON_ERROR);

            return $data['packages']['nox-php/framework'][0]['version_normalized'] ?? null;
        } catch (Exception) {
            return null;
        }
    }
}