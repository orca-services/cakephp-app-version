<?php
declare(strict_types=1);

namespace AppVersion\Middleware;

use Cake\Core\Configure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * AppVersionHeader middleware
 *
 * Adds a configurable vendor-prefixed "[vendor]-App-Version" HTTP header
 * containing the configured app version.
 *
 * If the vendor prefix is not set "X" is used.
 * If the app version is not set "Unknown" is returned.
 *
 * If you want to make sure the app version header is returned even in case of an error,
 * add this middleware before any error handling middleware.
 */
class AppVersionHeaderMiddleware implements MiddlewareInterface
{
    /**
     * Add app version header
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Server\RequestHandlerInterface $handler The request handler.
     * @return \Psr\Http\Message\ResponseInterface A response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $appVersionPrefix = Configure::read('App.versionPrefix', 'X');
        $appVersion = Configure::read('App.version', 'Unknown');

        $response = $response->withHeader($appVersionPrefix . '-App-Version', $appVersion);

        return $response;
    }
}
