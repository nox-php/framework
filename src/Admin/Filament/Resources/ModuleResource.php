<?php

namespace Nox\Framework\Admin\Filament\Resources;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Nox\Framework\Admin\Filament\Resources\ModuleResource\Pages;
use Nox\Framework\Extend\Models\Module;

class ModuleResource extends Resource
{
    protected static ?string $model = Module::class;

    protected static ?string $slug = 'extend/modules';

    protected static ?string $navigationGroup = 'Extend';

    protected static ?string $navigationIcon = 'heroicon-o-puzzle';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name'),
                Forms\Components\TextInput::make('version')
                    ->label('Version')
                    ->formatStateUsing(static fn (string $state): string => 'v'.$state),
                Forms\Components\TextInput::make('path')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->sortable()
                    ->searchable()
                    ->limit(),
                Tables\Columns\BadgeColumn::make('version')
                    ->label('Version')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('enabled')
                    ->label('Status')
                    ->enum([
                        true => 'Enabled',
                        false => 'Disabled',
                    ])
                    ->icons([
                        'heroicon-o-check' => true,
                        'heroicon-o-x' => false,
                    ])
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('enable-module')
                    ->label('Enable')
                    ->requiresConfirmation()
                    ->action('enableModule')
                    ->hidden(static fn (Module $record): bool => $record->enabled),
                Tables\Actions\Action::make('disable-module')
                    ->label('Disable')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action('disableModule')
                    ->hidden(static fn (Module $record): bool => ! $record->enabled),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->action('deleteModule'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('bulk-enable-modules')
                    ->label('Enable selected')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->action('bulkEnableModules'),
                Tables\Actions\BulkAction::make('bulk-disable-modules')
                    ->label('Disable selected')
                    ->icon('heroicon-o-x')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action('bulkDisableModules'),
                Tables\Actions\DeleteBulkAction::make()
                    ->action('bulkDeleteModules'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListModules::route('/'),
            'view' => Pages\ViewModule::route('/{record}'),
        ];
    }
}
