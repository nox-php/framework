<?php

namespace Nox\Framework\Admin\Filament\Resources;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Nox\Framework\Admin\Contracts\IsActivitySubject;
use Nox\Framework\Admin\Filament\RelationManagers\ActivitiesRelationManager;
use Nox\Framework\Admin\Filament\Resources\ActivityResource\Pages;
use Spatie\Activitylog\Models\Activity;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationIcon = 'heroicon-o-table';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('causer_type')
                    ->label('Causer type')
                    ->columnSpan([
                        'default' => 2,
                        'sm' => 1,
                    ]),
                Forms\Components\TextInput::make()
                    ->label('Causer id')
                    ->columnSpan([
                        'default' => 2,
                        'sm' => 1,
                    ]),
                Forms\Components\TextInput::make('subject_type')
                    ->label('Subject type')
                    ->columnSpan([
                        'default' => 2,
                        'sm' => 1,
                    ]),
                Forms\Components\TextInput::make('subject_id')
                    ->label('Subject id')
                    ->columnSpan([
                        'default' => 2,
                        'sm' => 1,
                    ]),
                Forms\Components\TextInput::make('description')
                    ->label('Description')
                    ->columnSpan(2),
                Forms\Components\KeyValue::make('properties.attributes')
                    ->label('Attributes')
                    ->columnSpan([
                        'default' => 2,
                        'sm' => 1,
                    ]),
                Forms\Components\KeyValue::make('properties.old')
                    ->label('Old')
                    ->columnSpan([
                        'default' => 2,
                        'sm' => 1,
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject')
                    ->hidden(function (Component $livewire) {
                        return method_exists($livewire, 'hideSubjectColumn')
                            ? call_user_func([$livewire, 'hideSubjectColumn'])
                            : $livewire instanceof ActivitiesRelationManager;
                    })
                    ->getStateUsing(function (Activity $record) {
                        if (! $record->subject || ! ($record->subject instanceof IsActivitySubject)) {
                            return new HtmlString('&mdash;');
                        }

                        $subject = $record->subject;

                        return $subject->getActivitySubjectDescription($record);
                    })
                    ->url(function (Activity $record) {
                        if (! $record->subject || ! $record->subject instanceof IsActivitySubject) {
                            return;
                        }

                        $resource = Filament::getModelResource($record->subject::class);

                        if (! $resource) {
                            return;
                        }

                        if (! $resource::hasPage('edit')) {
                            return;
                        }

                        return $resource::getUrl('edit', ['record' => $record->subject]) ?? null;
                    }, shouldOpenInNewTab: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Logged at')
                    ->dateTime()
                    ->sortable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
            'view' => Pages\ViewActivity::route('/{record}'),
        ];
    }
}
