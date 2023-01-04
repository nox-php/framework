<?php

namespace Nox\Framework\Extend\Enums;

enum ModuleStatus: string
{
    case NotFound = 'nox::modules.not_found';

    case BootFailed = 'nox::modules.boot_failed';

    case PublishSuccess = 'nox::modules.publish.success.body';

    case PublishFailed = 'nox::modules.publish.failed.body';

    case EnabledSuccess = 'nox::modules.enabled.success.body';

    case DisabledSuccess = 'nox::modules.disabled.success.body';

    case InstallSuccess = 'nox::modules.installed.success.body';

    case InstallFileNotFound = 'nox::modules.installed.failed.file_not_found';

    case InstallManifestNotFound = 'nox::modules.installed.failed.manifest_not_found';

    case InstallManifestLoadFailed = 'nox::modules.installed.failed.manifest_load_failed';

    case InstallInvalidManifest = 'nox::modules.installed.failed.invalid_manifest';

    case InstallAlreadyInstalled = 'nox::modules.installed.failed.already_installed';

    case InstallExtractFailed = 'nox::modules.installed.failed.extract_failed';

    case DeleteSuccess = 'nox::modules.delete.success.body';

    case DeleteFailed = 'nox::modules.delete.failed.body';
}
