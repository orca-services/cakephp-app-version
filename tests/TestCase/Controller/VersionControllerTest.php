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
 *
 * @coversDefaultClass \AppVersion\Controller\VersionController
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
                'action' => 'show',
                '_ext' => $extension,
                'pass' => [],
            ],
            'environment' => ['REQUEST_METHOD' => $method],
        ]);

        return new VersionController($request);
    }

    /**
     * Call the show action and render it
     *
     * @param string|null $extension The requested file extension.
     * @return Response
     */
    protected function renderShow(?string $extension = null): Response
    {
        $controller = $this->createController($extension);
        $controller->show();

        return $controller->render();
    }

    /**
     * Test show method returns JSON for the json extension
     *
     * @return void
     * @covers ::show
     */
    public function testShowJson(): void
    {
        $response = $this->renderShow('json');

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('application/json', $response->getType());
        self::assertSame('no-store', $response->getHeaderLine('Cache-Control'));
        self::assertSame(['app-version' => '1.2.3'], json_decode((string)$response->getBody(), true));
    }

    /**
     * Test show method returns JSON with "Unknown" if the app version is not set
     *
     * @return void
     * @covers ::show
     */
    public function testShowJsonAppVersionNotSet(): void
    {
        Configure::delete('App.version');

        $response = $this->renderShow('json');

        self::assertSame(['app-version' => 'Unknown'], json_decode((string)$response->getBody(), true));
    }

    /**
     * Test show method falls back to HTML without an extension
     *
     * @return void
     * @covers ::show
     */
    public function testShowHtmlFallback(): void
    {
        $response = $this->renderShow();

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('text/html', $response->getType());
        self::assertSame('no-store', $response->getHeaderLine('Cache-Control'));
        self::assertStringContainsString('<span class="app-version">1.2.3</span>', (string)$response->getBody());
    }

    /**
     * Test show method returns HTML for the html extension and escapes the version
     *
     * @return void
     * @covers ::show
     */
    public function testShowHtmlEscaping(): void
    {
        Configure::write('App.version', '1.2.3<script>');

        $response = $this->renderShow('html');
        $body = (string)$response->getBody();

        self::assertSame('text/html', $response->getType());
        self::assertStringContainsString('1.2.3&lt;script&gt;', $body);
        self::assertStringNotContainsString('<script>', $body);
    }

    /**
     * Test show method only allows reading
     *
     * @return void
     * @covers ::show
     */
    public function testShowPostNotAllowed(): void
    {
        $this->expectException(MethodNotAllowedException::class);

        $this->createController('json', 'POST')->show();
    }
}
