<?php
declare(strict_types=1);

namespace AppVersion;

use AppVersion\Command\AppVersionCommand;
use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;

/**
 * Plugin for AppVersion
 */
class AppVersionPlugin extends BasePlugin
{
    /**
     * The plugin has no routes of its own.
     *
     * @var bool
     */
    protected bool $routesEnabled = false;

    /**
     * Add the `app_version` command, if enabled.
     *
     * @param CommandCollection $commands The command collection to update.
     * @return CommandCollection
     */
    public function console(CommandCollection $commands): CommandCollection
    {
        if (AppVersion::isEnabled('cli')) {
            $commands->add(AppVersionCommand::defaultName(), AppVersionCommand::class);
        }

        return $commands;
    }
}
