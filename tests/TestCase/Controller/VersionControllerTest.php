<?php
declare(strict_types=1);

namespace AppVersion\Test\TestCase\Controller;

use AppVersion\Controller\VersionController;
use Cake\Core\Configure;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * VersionController Test Case
 */
class VersionControllerTest extends TestCase
{
    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        Configure::write('App.version', '1.2.3');
        Configure::delete('AppVersion');
        $this->loadPlugins(['AppVersion' => ['path' => dirname(__DIR__, 3) . DS]]);
    }

    /**
     * Create the controller for a request
     *
     * @param string|null $extension The requested file extension.
     * @param string $method The HTTP method.
     * @return VersionController
     */
    protected function createController(?string $extension = null, string $method = 'GET'): VersionController
    {
        $request = new ServerRequest([
            'url' => '/app-version' . ($extension ? '.' . $extension : ''),
            'params' => [
                'plugin' => 'AppVersion',
                'controller' => 'Version',
                'action' => 'index',
                '_ext' => $extension,
                'pass' => [],
            ],
            'environment' => ['REQUEST_METHOD' => $method],
        ]);

        return new VersionController($request);
    }

    /**
     * Call the index action and render it
     *
     * @param string|null $extension The requested file extension.
     * @return Response
     */
    protected function renderIndex(?string $extension = null): Response
    {
        $controller = $this->createController($extension);
        $controller->index();

        return $controller->render();
    }

    /**
     * Test index method returns JSON for the json extension
     *
     * @return void
     * @uses VersionController::index
     */
    public function testIndexJson(): void
    {
        $response = $this->renderIndex('json');

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('application/json', $response->getType());
        self::assertSame('no-store', $response->getHeaderLine('Cache-Control'));
        self::assertSame(['app-version' => '1.2.3'], json_decode((string)$response->getBody(), true));
    }

    /**
     * Test index method returns JSON with "Unknown" if the app version is not set
     *
     * @return void
     * @uses VersionController::index
     */
    public function testIndexJsonAppVersionNotSet(): void
    {
        Configure::delete('App.version');

        $response = $this->renderIndex('json');

        self::assertSame(['app-version' => 'Unknown'], json_decode((string)$response->getBody(), true));
    }

    /**
     * Test index method falls back to HTML without an extension
     *
     * @return void
     * @uses VersionController::index
     */
    public function testIndexHtmlFallback(): void
    {
        $response = $this->renderIndex();

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('text/html', $response->getType());
        self::assertSame('no-store', $response->getHeaderLine('Cache-Control'));
        self::assertStringContainsString('<span class="app-version">1.2.3</span>', (string)$response->getBody());
    }

    /**
     * Test index method returns HTML for the html extension and escapes the version
     *
     * @return void
     * @uses VersionController::index
     */
    public function testIndexHtmlEscaping(): void
    {
        Configure::write('App.version', '1.2.3<script>');

        $response = $this->renderIndex('html');
        $body = (string)$response->getBody();

        self::assertSame('text/html', $response->getType());
        self::assertStringContainsString('1.2.3&lt;script&gt;', $body);
        self::assertStringNotContainsString('<script>', $body);
    }

    /**
     * Test index method only allows reading
     *
     * @return void
     * @uses VersionController::index
     */
    public function testIndexPostNotAllowed(): void
    {
        $this->expectException(MethodNotAllowedException::class);

        $this->createController('json', 'POST')->index();
    }
}
