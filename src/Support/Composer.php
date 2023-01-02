<?php

namespace Nox\Framework\Support;

use Illuminate\Support\Composer as BaseComposer;

class Composer extends BaseComposer
{
    public function update(string $package, array|string $extra = []): int
    {
        $extra = $extra ? (array)$extra : [];

        $command = array_merge(
            $this->findComposer(),
            ['update', $package],
            $extra
        );

        return $this->getProcess($command)->run();
    }
}
