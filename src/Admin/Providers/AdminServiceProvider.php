<?php

namespace Nox\Framework\Admin\Providers;

use Filament\AvatarProviders\Contracts\AvatarProvider as AvatarProviderContract;
use Filament\Facades\Filament;
use Filament\PluginServiceProvider;
use Nox\Framework\Admin\Filament\AvatarProvider\AvatarProvider;
use Nox\Framework\Admin\Filament\FilamentManager;
use Nox\Framework\Admin\Filament\Resources\ActivityResource;
use Nox\Framework\Admin\Filament\Resources\UserResource;

class AdminServiceProvider extends PluginServiceProvider
{
    public static string $name = 'nox:admin';

    protected array $resources = [
        ActivityResource::class,
        UserResource::class
    ];

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->scoped('filament', FilamentManager::class);

        $this->app->singleton(AvatarProviderContract::class, AvatarProvider::class);

        $this->app->resolving('filament', function () {
            Filament::serving(static function () {
                if (config('nox.admin.register_theme')) {
                    Filament::registerTheme(mix('css/nox.css', 'nox'));
                }
            });
        });
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        $this->loadRoutesFrom(__DIR__ . '/../../../routes/admin.php');
    }
}
