<?php
declare(strict_types=1);

namespace AppVersion\Test\TestCase\Controller;

use AppVersion\Controller\VersionController;
use Cake\Core\Configure;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Http\Exception\NotFoundException;
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
    }

    /**
     * Create the controller for a request
     *
     * @param string $method The HTTP method.
     * @return VersionController
     */
    protected function createController(string $method = 'GET'): VersionController
    {
        $request = new ServerRequest(['environment' => ['REQUEST_METHOD' => $method]]);

        return new VersionController($request);
    }

    /**
     * Test index method
     *
     * @return void
     * @uses VersionController::index
     */
    public function testIndex(): void
    {
        Configure::write('AppVersion.endpoint.enabled', true);

        $response = $this->createController()->index();

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('application/json', $response->getType());
        self::assertSame('no-store', $response->getHeaderLine('Cache-Control'));
        self::assertSame('{"version":"1.2.3"}', (string)$response->getBody());
    }

    /**
     * Test index method when the app version is not set
     *
     * @return void
     * @uses VersionController::index
     */
    public function testIndexAppVersionNotSet(): void
    {
        Configure::write('AppVersion.endpoint.enabled', true);
        Configure::delete('App.version');

        $response = $this->createController()->index();

        self::assertSame('{"version":"Unknown"}', (string)$response->getBody());
    }

    /**
     * Test index method is not reachable while disabled
     *
     * @return void
     * @uses VersionController::index
     */
    public function testIndexDisabled(): void
    {
        $this->expectException(NotFoundException::class);

        $this->createController()->index();
    }

    /**
     * Test index method only allows reading
     *
     * @return void
     * @uses VersionController::index
     */
    public function testIndexPostNotAllowed(): void
    {
        Configure::write('AppVersion.endpoint.enabled', true);

        $this->expectException(MethodNotAllowedException::class);

        $this->createController('POST')->index();
    }
}
