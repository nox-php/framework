<?php

namespace Nox\Framework\Auth\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Nox\Framework\Database\Factories\UserFactory;
use Silber\Bouncer\Database\HasRolesAndAbilities;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasFactory, Notifiable, HasRolesAndAbilities;

    public static function getIdColumnName(): string
    {
        return 'id';
    }

    public static function getUsernameColumnName(): string
    {
        return 'name';
    }

    public static function getEmailColumnName(): string
    {
        return 'email';
    }

    public static function getEmailVerifiedAtColumnName(): string
    {
        return 'email_verified_at';
    }

    public static function getRememberTokenColumnName(): string
    {
        return 'remember_token';
    }

    public static function getDiscordIdColumnName(): string
    {
        return 'discord_id';
    }

    public static function getDiscordTokenColumnName(): string
    {
        return 'discord_token';
    }

    public static function getDiscordRefreshTokenColumnName(): string
    {
        return 'discord_refresh_token';
    }

    public static function getDiscordDiscriminatorColumnName(): string
    {
        return 'discord_discriminator';
    }

    public static function getDiscordAvatarColumnName(): string
    {
        return 'discord_avatar';
    }

    public static function getCreatedAtColumnName(): string
    {
        return 'created_at';
    }

    public static function getUpdatedAtColumnName(): string
    {
        return 'updated_at';
    }

    public function getFillable(): array
    {
        return [
            static::getUsernameColumnName(),
            static::getEmailColumnName(),
            static::getRememberTokenColumnName(),
            static::getEmailVerifiedAtColumnName(),
            static::getDiscordIdColumnName(),
            static::getDiscordTokenColumnName(),
            static::getDiscordRefreshTokenColumnName(),
            static::getDiscordDiscriminatorColumnName(),
            static::getDiscordAvatarColumnName(),
        ];
    }

    public function getAuthIdentifierName(): string
    {
        return static::getIdColumnName();
    }

    public function getEmailForPasswordReset()
    {
        return $this->{static::getEmailColumnName()};
    }

    public function getEmailForVerification()
    {
        return $this->{static::getEmailColumnName()};
    }

    public function hasVerifiedEmail(): bool
    {
        return $this->{static::getEmailVerifiedAtColumnName()} !== null;
    }

    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            static::getEmailVerifiedAtColumnName() => $this->freshTimestamp(),
        ])->save();
    }

    public function sendPasswordResetNotification($token): void
    {
    }

    public function getRememberTokenName(): string
    {
        return static::getRememberTokenColumnName();
    }

    public function getCreatedAtColumn(): string
    {
        return static::getCreatedAtColumnName();
    }

    public function getUpdatedAtColumn(): string
    {
        return static::getUpdatedAtColumnName();
    }

    public function canAccessFilament(): bool
    {
        return $this->can('view_admin');
    }

    public function getFilamentName(): string
    {
        return $this->{static::getUsernameColumnName()};
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
