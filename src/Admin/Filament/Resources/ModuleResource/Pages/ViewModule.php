<?php

namespace Nox\Framework\Admin\Filament\Resources\ModuleResource\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Nox\Framework\Admin\Filament\Resources\ModuleResource;
use Nox\Framework\Extend\Contracts\ModuleRepository;

class ViewModule extends ViewRecord
{
    protected static string $resource = ModuleResource::class;

    public function enableModule(
        ModuleRepository $modules
    ) {
        if ($modules->enable($this->record->name)) {
            Notification::make()
                ->success()
                ->title($this->record->name)
                ->body('Successfully enabled module')
                ->send();

            return redirect(ModuleResource::getUrl('view', ['record' => $this->record]));
        }

        Notification::make()
            ->success()
            ->title($this->record->name)
            ->body('Failed to enable module')
            ->send();
    }

    public function disableModule(
        ModuleRepository $modules
    ) {
        if ($modules->disable($this->record->name)) {
            Notification::make()
                ->success()
                ->title($this->record->name)
                ->body('Successfully disabled module')
                ->send();

            return redirect(ModuleResource::getUrl('view', ['record' => $this->record]));
        }

        Notification::make()
            ->success()
            ->title($this->record->name)
            ->body('Failed to disable module')
            ->send();
    }

    protected function getActions(): array
    {
        return [
            Action::make('enable-module')
                ->label('Enable')
                ->requiresConfirmation()
                ->action('enableModule')
                ->hidden(fn (): bool => $this->record->enabled),
            Action::make('disable-module')
                ->label('Disable')
                ->action('disableModule')
                ->requiresConfirmation()
                ->color('danger')
                ->hidden(fn (): bool => ! $this->record->enabled),
        ];
    }
}
