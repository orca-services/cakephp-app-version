<?php

namespace AppVersion\Test\TestCase;

use Cake\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * TestRequestHandler
 *
 * Test request handler for non double pass middleware tests
 */
class TestRequestHandler implements RequestHandlerInterface
{
    public $callable;

    public function __construct(?callable $callable = null)
    {
        $this->callable = $callable ?: function ($request) {
            return new Response();
        };
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return ($this->callable)($request);
    }
}
