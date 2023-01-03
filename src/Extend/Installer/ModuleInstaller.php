<?php

namespace Nox\Framework\Extend\Installer;

use Nox\Framework\Extend\Facades\Modules;
use Nox\Framework\Extend\Loader\ModuleLoader;
use Nox\Framework\Installer\Traits\InstallsComponents;

class ModuleInstaller
{
    use InstallsComponents;

    protected string $path;

    public function __construct(
        protected ModuleLoader $loader,
        ?string $path = null
    )
    {
        $this->path = $path ?? base_path('/modules');
    }

    public function install(string $path): ?string
    {
        if (empty($this->paths)) {
            return null;
        }

        if (!$zip = $this->getArchive($path)) {
            return null;
        }

        if (!$index = $this->findManifestIndex($zip)) {
            return null;
        }

        if (!$manifest = $this->getManifest($zip, $index)) {
            return null;
        }

        if (!$this->loader->validate($manifest)) {
            return null;
        }

        $name = $manifest['name'];

        if (Modules::find($name) !== null) {
            return null;
        }

        if (!$this->extract($zip, $name, $this->path)) {
            return null;
        }

        return $name;
    }
}
