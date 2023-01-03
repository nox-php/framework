<?php

namespace Nox\Framework\Admin\Filament\Resources;

use Closure;
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

    protected static ?int $navigationSort = 2;

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
                Forms\Components\TextInput::make('causer_id')
                    ->label('Causer id')
                    ->columnSpan([
                        'default' => 2,
                        'sm' => 1,
                    ])
                    ->suffixAction(static function (Closure $get, int $state) {
                        $resource = Filament::getModelResource($get('causer_type'));

                        if ($resource === null) {
                            return;
                        }

                        $url = null;
                        if ($resource::hasPage('view')) {
                            $url = $resource::getUrl('view', ['record' => $state]);
                        } else if ($resource::hasPage('edit')) {
                            $url = $resource::getUrl('edit', ['record' => $state]);
                        }

                        if ($url !== null) {
                            Forms\Components\Actions\Action::make('view-causer')
                                ->icon('heroicon-o-eye')
                                ->url($url, true);
                        }
                    }),
                Forms\Components\TextInput::make('subject_type')
                    ->label('Subject type')
                    ->columnSpan([
                        'default' => 2,
                        'sm' => 1,
                    ])
                    ->hidden(static fn($record): bool => $record->subject_id === null),
                Forms\Components\TextInput::make('subject_id')
                    ->label('Subject id')
                    ->columnSpan([
                        'default' => 2,
                        'sm' => 1,
                    ])
                    ->hidden(static fn($record): bool => $record->subject_id === null),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->columnSpan(2),
                Forms\Components\KeyValue::make('properties_except')
                    ->label('Properties')
                    ->afterStateHydrated(static function (Forms\Components\KeyValue $component, $record) {
                        $component->state(
                            collect($record->properties?->all() ?? [])
                                ->except(['old', 'attributes'])
                                ->all()
                        );
                    })
                    ->columnSpan(2),
                Forms\Components\KeyValue::make('properties_old')
                    ->label('Before')
                    ->helperText('Old model attributes')
                    ->columnSpan([
                        'default' => 2,
                        'sm' => 1,
                    ])
                    ->afterStateHydrated(static function (Forms\Components\KeyValue $component, $record) {
                        if ($record->properties === null) {
                            return;
                        }

                        $component->state(
                            $record->properties['old'] ?? []
                        );
                    })
                    ->hidden(static fn($record): bool => $record->subject_id === null),
                Forms\Components\KeyValue::make('properties_attributes')
                    ->label('After')
                    ->helperText('New model attributes')
                    ->columnSpan([
                        'default' => 2,
                        'sm' => 1,
                    ])
                    ->afterStateHydrated(static function (Forms\Components\KeyValue $component, $record) {
                        if ($record->properties === null) {
                            return;
                        }

                        $component->state(
                            $record->properties['attributes'] ?? []
                        );
                    })
                    ->hidden(static fn($record): bool => $record->subject_id === null)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->limit(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject')
                    ->hidden(function (Component $livewire) {
                        return method_exists($livewire, 'hideSubjectColumn')
                            ? call_user_func([$livewire, 'hideSubjectColumn'])
                            : $livewire instanceof ActivitiesRelationManager;
                    })
                    ->getStateUsing(function (Activity $record) {
                        if (!$record->subject || !($record->subject instanceof IsActivitySubject)) {
                            return new HtmlString('&mdash;');
                        }

                        $subject = $record->subject;

                        return $subject->getActivitySubjectDescription($record);
                    })
                    ->url(function (Activity $record) {
                        if (!$record->subject || !$record->subject instanceof IsActivitySubject) {
                            return;
                        }

                        $resource = Filament::getModelResource($record->subject::class);

                        if (!$resource) {
                            return;
                        }

                        if (!$resource::hasPage('edit')) {
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