<?php
declare(strict_types=1);

namespace AppVersion;

use AppVersion\Command\AppVersionCommand;
use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;
use Cake\Routing\RouteBuilder;

/**
 * Plugin for AppVersion
 */
class AppVersionPlugin extends BasePlugin
{
    /**
     * Connect the version endpoint, if enabled.
     *
     * The endpoint is only connected if `AppVersion.endpoint.enabled` is true.
     * Its path is configured with `AppVersion.endpoint.path` (default `/version`).
     * Middleware registered in the application routes can be applied to it
     * with `AppVersion.endpoint.middleware`, e.g. to restrict access.
     *
     * @param RouteBuilder $routes The route builder to update.
     * @return void
     */
    public function routes(RouteBuilder $routes): void
    {
        if (AppVersion::isEnabled('endpoint')) {
            $path = '/' . ltrim((string)AppVersion::config('endpoint.path'), '/');

            $route = $routes->get(
                $path,
                ['plugin' => 'AppVersion', 'controller' => 'Version', 'action' => 'index'],
                'AppVersion.version',
            );

            $middleware = (array)AppVersion::config('endpoint.middleware');
            if ($middleware !== []) {
                $route->setMiddleware($middleware);
            }
        }

        parent::routes($routes);
    }

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
