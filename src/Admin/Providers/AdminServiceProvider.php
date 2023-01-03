<?php

namespace Nox\Framework\Admin\Providers;

use Composer\InstalledVersions;
use Filament\AvatarProviders\Contracts\AvatarProvider as AvatarProviderContract;
use Filament\Facades\Filament;
use Filament\PluginServiceProvider;
use Illuminate\Contracts\View\View;
use Nox\Framework\Admin\Filament\AvatarProvider\AvatarProvider;
use Nox\Framework\Admin\Filament\FilamentManager;
use Nox\Framework\Admin\Filament\Pages\Health as HealthPage;
use Nox\Framework\Admin\Filament\Pages\Settings;
use Nox\Framework\Admin\Filament\Resources\ActivityResource;
use Nox\Framework\Admin\Filament\Resources\UserResource;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Facades\Health;

class AdminServiceProvider extends PluginServiceProvider
{
    public static string $name = 'nox:admin';

    protected array $resources = [
        ActivityResource::class,
        UserResource::class
    ];

    protected array $pages = [
        Settings::class,
        HealthPage::class
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

                Filament::registerRenderHook(
                    'footer.end',
                    static fn(): View => view(
                        'nox::filament.versions',
                        [
                            'versions' => [
                                'nox' => InstalledVersions::getPrettyVersion('nox-php/framework'),
                                'php' => PHP_VERSION
                            ]
                        ]
                    )
                );
            });
        });
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        $this->loadRoutesFrom(__DIR__ . '/../../../routes/admin.php');

        Health::checks([
            DebugModeCheck::new(),
            EnvironmentCheck::new(),
            OptimizedAppCheck::new(),
            CacheCheck::new(),
            ScheduleCheck::new(),
            QueueCheck::new(),
            UsedDiskSpaceCheck::new()
        ]);
    }
}
