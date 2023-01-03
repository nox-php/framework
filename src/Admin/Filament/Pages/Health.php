<?php

namespace Nox\Framework\Admin\Filament\Pages;

use Carbon\Carbon;
use Filament\Pages\Actions\Action;
use Filament\Pages\Page;
use Spatie\Health\ResultStores\ResultStore;

class Health extends Page
{
    protected static string $view = 'nox::filament.pages.health';

    protected static ?string $slug = 'system/health';

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?int $navigationSort = 75;

    protected function getActions(): array
    {
        return transformer(
            'nox.health.actions',
            [
                Action::make('refresh-health')
                    ->label('Refresh')
                    ->action('refresh')
            ]
        );
    }

    protected function getViewData(): array
    {
        $lastResults = app(ResultStore::class)
            ->latestResults();

        $storedCheckResults = $lastResults?->storedCheckResults;

        return [
            'lastRanAt' => Carbon::parse($lastResults?->finishedAt),
            'storedCheckResults' => $storedCheckResults,
        ];
    }
}
