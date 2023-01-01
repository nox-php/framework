<?php

namespace Nox\Framework\Installer\Jobs;

use Composer\InstalledVersions;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class CheckForNoxUpdates implements ShouldQueue
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

        if ($version !== $installedVersion) {
            info('New Version available!');
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
