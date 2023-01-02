<?php

namespace Nox\Framework\Admin\Filament\Notifications;

use Closure;
use Filament\Notifications\Notification as NotificationBase;

class Notification extends NotificationBase
{
    protected bool $isBanner = false;

    public function banner(bool|Closure $condition = true): static
    {
        $this->isBanner = value($condition);

        return $this;
    }

    public function isBanner(): bool
    {
        return $this->isBanner;
    }

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'banner' => $this->isBanner()
        ];
    }

    public static function fromArray(array $data): static
    {
        $notification = parent::fromArray($data);
        $notification->banner($data['banner'] ?? false);

        return $notification;
    }
}
