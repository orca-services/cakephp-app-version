<?php
declare(strict_types=1);

namespace AppVersion\Test\TestCase\Middleware;

use AppVersion\Middleware\AppVersionHeaderMiddleware;
use AppVersion\Test\TestCase\TestRequestHandler;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * AppVersionHeaderMiddleware Test Case
 */
class AppVersionHeaderMiddlewareTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \AppVersion\Middleware\AppVersionHeaderMiddleware
     */
    protected AppVersionHeaderMiddleware $appVersionHeader;

    /** @var ServerRequestInterface|ServerRequest  */
    protected ServerRequestInterface $request;

    /** @var RequestHandlerInterface|TestRequestHandler  */
    protected RequestHandlerInterface $handler;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->appVersionHeader = new AppVersionHeaderMiddleware();
        $this->request = new ServerRequest();
        $this->handler = new TestRequestHandler();
    }

    /**
     * Test process method
     *
     * @return void
     * @uses \AppVersion\Middleware\AppVersionHeaderMiddleware::process()
     */
    public function testProcess(): void
    {
        $origAppVersion = Configure::read('App.version');
        $origAppVersionPrefix = Configure::read('App.versionPrefix');
        $origDeploymentTime = Configure::read('App.deploymentTime');

        $appVersion = '1.2.3';
        Configure::write('App.version', $appVersion);
        $appVersionPrefix = 'vendor';
        Configure::write('App.versionPrefix', $appVersionPrefix);
        $deploymentTime = "2024-11-07";
        Configure::write('App.deploymentTime', $deploymentTime);

        $response = $this->appVersionHeader->process($this->request, $this->handler);

        self::assertSame($appVersion, $response->getHeaderLine($appVersionPrefix . '-App-Version'));
        self::assertSame($deploymentTime, $response->getHeaderLine($appVersionPrefix . '-Deployment-Time'));

        Configure::write('App.version', $origAppVersion);
        Configure::write('App.versionPrefix', $origAppVersionPrefix);
        Configure::write('App.deploymentTime', $origDeploymentTime);
    }

    /**
     * Test process method when app version is not set
     *
     * @return void
     * @uses \AppVersion\Middleware\AppVersionHeaderMiddleware::process()
     */
    public function testProcessAppVersionNotSet(): void
    {
        $origAppVersion = Configure::read('App.version');
        $origAppVersionPrefix = Configure::read('App.versionPrefix');

        Configure::delete('App.version');
        $appVersionPrefix = 'vendor';
        Configure::write('App.versionPrefix', $appVersionPrefix);

        $response = $this->appVersionHeader->process($this->request, $this->handler);

        self::assertSame('Unknown', $response->getHeaderLine($appVersionPrefix . '-App-Version'));

        Configure::write('App.version', $origAppVersion);
        Configure::write('App.versionPrefix', $origAppVersionPrefix);
    }

    /**
     * Test process method when app versionPrefix is not set
     *
     * @return void
     * @uses \AppVersion\Middleware\AppVersionHeaderMiddleware::process()
     */
    public function testProcessAppVersionPrefixNotSet(): void
    {
        $origAppVersion = Configure::read('App.version');
        $origAppVersionPrefix = Configure::read('App.versionPrefix');

        $appVersion = '1.2.3';
        Configure::write('App.version', $appVersion);
        Configure::delete('App.versionPrefix');

        $response = $this->appVersionHeader->process($this->request, $this->handler);

        self::assertSame($appVersion, $response->getHeaderLine('X-App-Version'));

        Configure::write('App.version', $origAppVersion);
        Configure::write('App.versionPrefix', $origAppVersionPrefix);
    }

    /**
     * Test process method when Deployment time is not set
     *
     * @return void
     * @uses \AppVersion\Middleware\AppVersionHeaderMiddleware::process()
     */
    public function testProcessDeploymentTimeNotSet(): void
    {
        $origDeploymentTime = Configure::read('App.deploymentTime');
        $origAppVersionPrefix = Configure::read('App.versionPrefix');

        Configure::delete('App.deploymentTime');
        $appVersionPrefix = 'vendor';
        Configure::write('App.versionPrefix', $appVersionPrefix);

        $response = $this->appVersionHeader->process($this->request, $this->handler);

        self::assertSame('Unknown', $response->getHeaderLine($appVersionPrefix . '-Deployment-Time'));

        Configure::write('App.deploymentTime', $origDeploymentTime);
        Configure::write('App.versionPrefix', $origAppVersionPrefix);
    }
}
