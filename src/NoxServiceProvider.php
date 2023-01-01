<?php

namespace Nox\Framework;

use Illuminate\Support\AggregateServiceProvider;
use Nox\Framework\Admin\Providers\AdminServiceProvider;
use Nox\Framework\Auth\Providers\AuthServiceProvider;
use Nox\Framework\Installer\Providers\InstallerServiceProvider;
use Nox\Framework\Settings\Providers\SettingsServiceProvider;
use Nox\Framework\Transformer\Provider\TransformerServiceProvider;

class NoxServiceProvider extends AggregateServiceProvider
{
    protected $providers = [
        TransformerServiceProvider::class,
        SettingsServiceProvider::class,
        AuthServiceProvider::class,
        InstallerServiceProvider::class,
        AdminServiceProvider::class,
    ];

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/nox.php', 'nox');

        parent::register();

        info('Nox registered!');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/nox.php' => config_path('nox.php'),
            ]);

            $this->publishes([
                __DIR__.'/../dist' => public_path('nox'),
            ], 'assets');

            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        info('Nox booted');
    }
}
