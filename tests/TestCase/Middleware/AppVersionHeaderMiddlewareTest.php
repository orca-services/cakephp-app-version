<?php
declare(strict_types=1);

namespace AppVersion\Test\TestCase\Middleware;

use AppVersion\Middleware\AppVersionHeaderMiddleware;
use App\Test\TestCase\TestRequestHandler;
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

        $appVersion = '1.2.3';
        Configure::write('App.version', $appVersion);
        $appVersionPrefix = 'vendor';
        Configure::write('App.versionPrefix', $appVersionPrefix);

        $response = $this->appVersionHeader->process($this->request, $this->handler);

        self::assertSame($appVersion, $response->getHeaderLine($appVersionPrefix . '-App-Version'));

        Configure::write('App.version', $origAppVersion);
        Configure::write('App.versionPrefix', $origAppVersionPrefix);
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
}
