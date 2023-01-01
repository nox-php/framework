<?php

namespace Nox\Framework\Installer\Jobs;

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
        // Retrigger release
        $data = Http::get(static::$baseUrl . 'nox-php/framework~dev.json');

        dd($data->body());
    }
}
