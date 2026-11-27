<?php
declare(strict_types=1);

namespace AppVersion\Test\TestApp;

use AppVersion\AppVersionPlugin;
use Cake\Http\BaseApplication;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\RouteBuilder;

/**
 * Minimal application to run the plugin's commands in console integration tests
 */
class TestApplication extends BaseApplication
{
    /**
     * Load only this plugin, without an application `config/bootstrap.php`.
     *
     * @return void
     */
    public function bootstrap(): void
    {
        $this->addPlugin(AppVersionPlugin::class);
    }

    /**
     * @inheritDoc
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        return $middlewareQueue;
    }

    /**
     * No application routes, so no `config/routes.php` is required.
     *
     * @param RouteBuilder $routes The route builder.
     * @return void
     */
    public function routes(RouteBuilder $routes): void
    {
    }
}
