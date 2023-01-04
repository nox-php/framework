<?php

namespace Nox\Framework\Extend\Enums;

enum ModuleStatus: string
{
    case NotFound = 'nox::module.not_found';

    case BootFailed = 'nox::module.boot_failed';

    case PublishSuccess = 'nox::module.publish.success.body';

    case PublishFailed = 'nox::module.publish.failed.body';

    case EnabledSuccess = 'nox::module.enabled.success.body';

    case DisabledSuccess = 'nox::module.disabled.success.body';

    case InstallSuccess = 'nox::module.installed.success.body';

    case InstallFileNotFound = 'nox::module.installed.failed.file_not_found';

    case InstallManifestNotFound = 'nox::module.installed.failed.manifest_not_found';

    case InstallManifestLoadFailed = 'nox::module.installed.failed.manifest_load_failed';

    case InstallInvalidManifest = 'nox::module.installed.failed.invalid_manifest';

    case InstallAlreadyInstalled = 'nox::module.installed.failed.already_installed';

    case InstallExtractFailed = 'nox::module.installed.failed.extract_failed';

    case DeleteSuccess = 'nox::module.delete.success.body';

    case DeleteFailed = 'nox::module.delete.failed.body';
}
