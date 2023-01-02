<?php

namespace Nox\Framework\Support;

use Illuminate\Support\Str;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class Composer
{
    protected string $basePath;

    protected array $env;

    protected string $output = '';

    protected string $errorOutput = '';

    public function __construct(?string $basePath = null)
    {
        $this->basePath = Str::finish($basePath ?? base_path(), '/');
        $this->env = $this->getEnvironment();
    }

    public function run(string $command, string $package, array $extraParams = []): int
    {
        $this->clearOutput();

        $params = [
            $this->phpBinary(),
            'vendor/bin/composer',
            '--no-ansi',
            $command,
            $package,
            ...$extraParams
        ];

        $process = $this->newSymfonyProcess($params, $this->basePath);

        $status = $process->run(env: $this->env);

        $this->output = $process->getOutput();
        $this->errorOutput = $process->getErrorOutput();

        return $status;
    }

    public function update(string $package, array $extraParams = []): int
    {
        return $this->run('update', $package, $extraParams);
    }

    public function getOutput(): string
    {
        return $this->output;
    }

    public function getErrorOutput(): string
    {
        return $this->errorOutput;
    }

    protected function newSymfonyProcess($command, $path = null): Process
    {
        if (!is_array($command)) {
            $command = (string)$command;
        }

        $process = is_string($command) && method_exists(Process::class, 'fromShellCommandLine')
            ? Process::fromShellCommandline($command, $path ?? $this->basePath, $this->env)
            : new Process($command, $path ?? $this->basePath, $this->env);

        $process->setTimeout(null);

        return $process;
    }

    protected function phpBinary(): string
    {
        return (new PhpExecutableFinder())->find();
    }

    protected function getEnvironment(): array
    {
        $env = collect(getenv())->only(['HOME', 'LARAVEL_SAIL', 'COMPOSER_HOME', 'APPDATA', 'LOCALAPPDATA']);

        if (!$env->has('HOME') && $env->get('LARAVEL_SAIL') === '1') {
            $env['HOME'] = '/home/sail';
        }

        return $env->all();
    }

    protected function clearOutput(): void
    {
        $this->output = '';
        $this->errorOutput = '';
    }
}
