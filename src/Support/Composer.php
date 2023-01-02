<?php

namespace Nox\Framework\Support;

use Composer\Console\Application;
use Composer\Factory;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class Composer
{
    protected ?BufferedOutput $output = null;

    public function __construct()
    {
        putenv('COMPOSER_HOME=' . __DIR__ . '/vendor/bin/composer');
    }

    public function run(string $command, array $extraParameters = []): int
    {
        $input = new ArrayInput([
            ...$extraParameters,
            'command' => $command
        ]);

        $input->setInteractive(false);

        $this->output = new BufferedOutput();

        $composer = new Application();
        $composer->setAutoExit(false);

        return $composer->run($input, $this->output);
    }

    public function require(string $package): int
    {
        return $this->run('require', [
            'packages' => [$package]
        ]);
    }

    public function update(string $package): int
    {
        return $this->run('update', [
            'packages' => [$package]
        ]);
    }

    public function getOutput(): ?BufferedOutput
    {
        return $this->output;
    }
}
