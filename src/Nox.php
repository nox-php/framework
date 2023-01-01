<?php

namespace Nox\Framework;

use Illuminate\Support\Facades\Storage;

class Nox
{
    public static function installed(): bool
    {
        return Storage::exists('nox.installed');
    }
}
