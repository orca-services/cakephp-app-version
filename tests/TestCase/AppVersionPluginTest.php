<?php
declare(strict_types=1);

namespace AppVersion\Test\TestCase;

use AppVersion\AppVersionPlugin;
use AppVersion\Command\AppVersionCommand;
use Cake\Console\CommandCollection;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\Routing\Exception\MissingRouteException;
use Cake\Routing\RouteBuilder;
use Cake\Routing\RouteCollection;
use Cake\TestSuite\TestCase;

/**
 * AppVersionPlugin Test Case
 */
class AppVersionPluginTest extends TestCase
{
    /**
     * @var RouteCollection
     */
    protected RouteCollection $collection;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        Configure::delete('AppVersion');
        $this->collection = new RouteCollection();
    }

    /**
     * Load the plugin routes into the test route collection
     *
     * @return void
     */
    protected function loadPluginRoutes(): void
    {
        $builder = new RouteBuilder($this->collection, '/');
        (new AppVersionPlugin())->routes($builder);
    }

    /**
     * Parse a request with the test route collection
     *
     * @param string $url The URL.
     * @param string $method The HTTP method.
     * @return array The route parameters.
     */
    protected function parse(string $url, string $method = 'GET'): array
    {
        $request = new ServerRequest(['url' => $url, 'environment' => ['REQUEST_METHOD' => $method]]);

        return $this->collection->parseRequest($request);
    }

    /**
     * Test no routes are connected by default
     *
     * @return void
     * @uses AppVersionPlugin::routes
     */
    public function testRoutesDisabledByDefault(): void
    {
        $this->loadPluginRoutes();

        self::assertSame([], $this->collection->routes());
    }

    /**
     * Test the endpoint route with the default path
     *
     * @return void
     * @uses AppVersionPlugin::routes
     */
    public function testRoutesEndpoint(): void
    {
        Configure::write('AppVersion.endpoint.enabled', true);
        $this->loadPluginRoutes();

        $params = $this->parse('/version');

        self::assertSame('AppVersion', $params['plugin']);
        self::assertSame('Version', $params['controller']);
        self::assertSame('index', $params['action']);
        self::assertSame('AppVersion.version', $params['_name']);
    }

    /**
     * Test the endpoint route with a custom path and middleware
     *
     * @return void
     * @uses AppVersionPlugin::routes
     */
    public function testRoutesEndpointCustomPathAndMiddleware(): void
    {
        Configure::write('AppVersion.endpoint', [
            'enabled' => true,
            'path' => 'status/version',
            'middleware' => ['auth'],
        ]);
        $this->loadPluginRoutes();

        $params = $this->parse('/status/version');

        self::assertSame('Version', $params['controller']);
        self::assertSame(['auth'], $params['_middleware']);
    }

    /**
     * Test the endpoint route only accepts GET requests
     *
     * @return void
     * @uses AppVersionPlugin::routes
     */
    public function testRoutesEndpointPostNotRouted(): void
    {
        Configure::write('AppVersion.endpoint.enabled', true);
        $this->loadPluginRoutes();

        $this->expectException(MissingRouteException::class);

        $this->parse('/version', 'POST');
    }

    /**
     * Test the command is added by default
     *
     * @return void
     * @uses AppVersionPlugin::console
     */
    public function testConsole(): void
    {
        $commands = (new AppVersionPlugin())->console(new CommandCollection());

        self::assertTrue($commands->has('app_version'));
        self::assertSame(AppVersionCommand::class, $commands->get('app_version'));
    }

    /**
     * Test the command is not added if the CLI output is disabled
     *
     * @return void
     * @uses AppVersionPlugin::console
     */
    public function testConsoleDisabled(): void
    {
        Configure::write('AppVersion.cli.enabled', false);

        $commands = (new AppVersionPlugin())->console(new CommandCollection());

        self::assertFalse($commands->has('app_version'));
    }
}
