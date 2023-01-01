<?php

namespace Nox\Framework\Installer\Jobs;

use Composer\InstalledVersions;
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

        $data = Http::get(static::$baseUrl . 'nox-php/framework.json');

        dd(json_decode($data->body()));
    }
}
