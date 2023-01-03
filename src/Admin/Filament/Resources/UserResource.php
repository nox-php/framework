<?php

namespace Nox\Framework\Admin\Filament\Resources;

use Closure;
use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Nox\Framework\Admin\Filament\Resources\UserResource\Pages;
use Nox\Framework\Auth\Models\User;
use Silber\Bouncer\BouncerFacade;
use Silber\Bouncer\Database\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $slug = 'system/users';

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return transformer(
            'nox.user.resource.form',
            $form
                ->columns([
                    'sm' => 3,
                    'lg' => null
                ])
                ->schema(
                    form([
                        Forms\Components\Card::make()
                            ->columns([
                                'sm' => 2
                            ])
                            ->columnSpan([
                                'sm' => 2
                            ])
                            ->schema([
                                Forms\Components\TextInput::make(User::getUsernameColumnName())
                                    ->label('Username')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make(User::getEmailColumnName())
                                    ->label('Email address')
                                    ->required()
                                    ->email()
                                    ->maxLength(255)
                                    ->unique(ignorable: static fn(?User $record): ?User => $record),
                                Forms\Components\Hidden::make('is_super_admin'),
                                Forms\Components\Select::make('roles')
                                    ->label('Roles')
                                    ->multiple()
                                    ->required()
                                    ->afterStateHydrated(static function (?User $record, Closure $set) {
                                        $set('is_super_admin', $record?->can('*') ?? false);

                                        if ($record === null) {
                                            return;
                                        }

                                        $set('roles', $record->getRoles()->all());
                                    })
                                    ->options(Role::all()->pluck('title', 'name')->all())
                                    ->saveRelationshipsUsing(static function (User $record, Closure $get, array $state) {
                                        $record->roles()->detach();

                                        BouncerFacade::assign($state)->to($record);
                                        BouncerFacade::refreshFor($record);

                                        if (
                                            Filament::auth()->id() === $record->getKey() &&
                                            $get('is_super_admin') &&
                                            !$record->can('*')
                                        ) {
                                            BouncerFacade::assign('superadmin')->to($record);
                                            BouncerFacade::refreshFor($record);
                                        }
                                    })
                            ]),
                        Forms\Components\Card::make()
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\Placeholder::make('discord_name')
                                    ->label('Discord name')
                                    ->content(static fn(?User $record): string => $record->discord_name),
                                Forms\Components\Placeholder::make(User::getCreatedAtColumnName())
                                    ->label('Created at')
                                    ->content(static fn(?User $record): string => $record?->{User::getCreatedAtColumnName()}?->diffForHumans() ?? '-'),
                                Forms\Components\Placeholder::make(User::getUpdatedAtColumnName())
                                    ->label('Updated at')
                                    ->content(static fn(?User $record): string => $record?->{User::getUpdatedAtColumnName()}?->diffForHumans() ?? '-'),
                            ])
                    ])->build()
                )
        );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(
                transformer(
                    'nox.user.resource.table.columns',
                    [
                        Tables\Columns\ImageColumn::make('avatar')
                            ->label('Avatar')
                            ->circular()
                            ->getStateUsing(static function (User $record) {
                                return app(AvatarProvider::class)->get($record);
                            }),
                        Tables\Columns\TextColumn::make(User::getUsernameColumnName())
                            ->label('Username'),
                        Tables\Columns\TextColumn::make(User::getEmailColumnName())
                            ->label('Email address'),
                        Tables\Columns\BadgeColumn::make('discord_name')
                            ->label('Discord name'),
                        Tables\Columns\TextColumn::make(User::getCreatedAtColumnName())
                            ->label('Created at')
                            ->date(),
                        Tables\Columns\TextColumn::make(User::getUpdatedAtColumnName())
                            ->label('Updated at')
                            ->date()
                    ]
                )
            );
    }

    public static function getPages(): array
    {
        return transformer(
            'nox.user.resource.pages',
            [
                'index' => Pages\ListUsers::route('/'),
                'edit' => Pages\EditUser::route('/{record}/edit'),
            ]
        );
    }

    public static function getRelations(): array
    {
        return transformer(
            'nox.user.resource.relations',
            []
        );
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            User::getUsernameColumnName(),
            User::getEmailColumnName(),
            User::getDiscordDiscriminatorColumnName()
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return transformer(
            'nox.themes.resource.title',
            $record->discord_name,
            [
                'user' => $record
            ]
        );
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return transformer(
            'nox.users.resource.search.details',
            [
                'Email address' => $record->{User::getEmailColumnName()},
                'Created at' => $record->{User::getCreatedAtColumnName()}?->diffForHumans() ?? '-'
            ],
            [
                'user' => $record
            ]
        );
    }

    protected static function getNavigationBadge(): ?string
    {
        return number_format(User::query()->count());
    }
}
