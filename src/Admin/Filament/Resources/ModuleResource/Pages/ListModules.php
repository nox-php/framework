<?php

namespace Nox\Framework\Admin\Filament\Resources\ModuleResource\Pages;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Nox\Framework\Admin\Filament\Resources\ModuleResource;
use Nox\Framework\Extend\Contracts\ModuleRepository;

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
