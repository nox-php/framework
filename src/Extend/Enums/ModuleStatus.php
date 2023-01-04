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

    case DeleteSuccess = 'nox::modules.delete.success.body';

    case DeleteFailed = 'nox::modules.delete.failed.body';
}
