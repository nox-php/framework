<?php

namespace Nox\Framework\Admin\Providers;

use Filament\Facades\Filament;
use Filament\PluginServiceProvider;
use Illuminate\Contracts\View\View;
use Nox\Framework\Admin\Filament\FilamentManager;

class AdminServiceProvider extends PluginServiceProvider
{
    public static string $name = 'nox:admin';

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->scoped('filament', FilamentManager::class);

        $this->app->resolving('filament', function () {
            Filament::serving(static function () {
                if (config('nox.admin.register_theme')) {
//                    Filament::registerTheme(mix('css/nox.css', 'nox'));
                }

                Filament::registerRenderHook(
                    'content.start',
                    static fn(): View => view('nox::filament.notifications.banners')
                );
            });
        });
    }
}
