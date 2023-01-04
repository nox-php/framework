<?php

namespace Nox\Framework\Admin\Filament\Resources\ModuleResource\Pages;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Collection;
use Nox\Framework\Admin\Filament\Resources\ModuleResource;
use Nox\Framework\Extend\Contracts\ModuleRepository;
use Nox\Framework\Extend\Models\Module;

class ListModules extends ListRecords
{
    protected static string $resource = ModuleResource::class;

    public function installModules(
        ModuleRepository $modules,
        ComponentContainer $form,
        array $data
    )
    {
        [$component] = $form->getComponents();

        $storage = $component->getDisk();

        foreach ($data['modules'] as $path) {
            $file = $storage->path($path);

            if ($modules->install($file)) {
                Notification::make()
                    ->success()
                    ->title('Module successfully installed')
                    ->send();
            } else {
                Notification::make()
                    ->danger()
                    ->title('Module unsuccessfully installed')
                    ->send();
            }
        }
    }

    public function enableModule(
        ModuleRepository $modules,
        Module $record
    )
    {
        if ($modules->enable($record->name)) {
            Notification::make()
                ->success()
                ->title($record->name)
                ->body('Successfully enabled module')
                ->send();

            return redirect(ModuleResource::getUrl());
        }

        Notification::make()
            ->success()
            ->title($record->name)
            ->body('Failed to enable module')
            ->send();
    }

    public function bulkEnableModules(
        ModuleRepository $modules,
        Collection $records
    )
    {
        foreach ($records as $record) {
            if ($modules->enable($record->name)) {
                Notification::make()
                    ->success()
                    ->title($record->name)
                    ->body('Successfully enabled module')
                    ->send();
            } else {
                Notification::make()
                    ->success()
                    ->title($record->name)
                    ->body('Failed to enable module')
                    ->send();
            }
        }

        return redirect(ModuleResource::getUrl());
    }

    public function disableModule(
        ModuleRepository $modules,
        Module $record
    )
    {
        if ($modules->disable($record->name)) {
            Notification::make()
                ->success()
                ->title($record->name)
                ->body('Successfully disabled module')
                ->send();

            return redirect(ModuleResource::getUrl());
        }

        Notification::make()
            ->success()
            ->title($record->name)
            ->body('Failed to disable module')
            ->send();
    }

    public function bulkDisableModules(
        ModuleRepository $modules,
        Collection $records
    )
    {
        foreach ($records as $record) {
            if ($modules->disable($record->name)) {
                Notification::make()
                    ->success()
                    ->title($record->name)
                    ->body('Successfully disabled module')
                    ->send();
            } else {
                Notification::make()
                    ->success()
                    ->title($record->name)
                    ->body('Failed to disable module')
                    ->send();
            }
        }

        return redirect(ModuleResource::getUrl());
    }

    public function deleteModule(
        ModuleRepository $modules,
        Module $record
    )
    {
        if ($modules->delete($record->name)) {
            Notification::make()
                ->success()
                ->title($record->name)
                ->body('Successfully deleted module')
                ->send();

            return redirect(ModuleResource::getUrl());
        }

        Notification::make()
            ->success()
            ->title($record->name)
            ->body('Failed to delete module')
            ->send();
    }

    public function bulkDeleteModules(
        ModuleRepository $modules,
        Collection $records
    )
    {
        foreach ($records as $record) {
            if ($modules->delete($record->name)) {
                Notification::make()
                    ->success()
                    ->title($record->name)
                    ->body('Successfully deleted module')
                    ->send();
            } else {
                Notification::make()
                    ->success()
                    ->title($record->name)
                    ->body('Failed to delete module')
                    ->send();
            }
        }

        return redirect(ModuleResource::getUrl());
    }

    protected function getActions(): array
    {
        return [
            Action::make('install-module')
                ->label('Install modules')
                ->action('installModules')
                ->form([
                    FileUpload::make('modules')
                        ->disableLabel()
                        ->multiple()
                        ->directory('modules-tmp')
                        ->minFiles(1)
                        ->acceptedFileTypes([
                            'application/zip',
                            'application/x-zip-compressed',
                            'multipart/x-zip',
                        ])
                ]),
        ];
    }
}
