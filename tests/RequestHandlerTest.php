<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Eureka\Component\Http\Tests\Helper;

use Eureka\Component\Http\Server\RequestHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class RequestHandlerTest
 *
 * @author Romain Cottard
 */
class RequestHandlerTest extends TestCase
{
    /**
     * @return void
     */
    public function testCanInstantiateRequestHandler(): void
    {
        $responseMock = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $requestHandler = new RequestHandler($responseMock);

        $this->assertInstanceOf(RequestHandlerInterface::class, $requestHandler);
    }

    /**
     * @return void
     */
    public function testCanHandleRequest(): void
    {
        $responseMock      = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $serverRequestMock = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $middlewareMock    = new class implements MiddlewareInterface {
            public function process(ServerRequestInterface $serverRequest, RequestHandlerInterface $handler): ResponseInterface
            {
                return $handler->handle($serverRequest);
            }
        };

        $requestHandler = new RequestHandler($responseMock, [clone $middlewareMock]);
        $requestHandler = $requestHandler->withMiddleware(clone $middlewareMock);

        /** @var ServerRequestInterface $serverRequestMock */
        $response = $requestHandler->handle($serverRequestMock);

        $this->assertEquals($responseMock, $response);
    }

    /**
     * @return void
     */
    public function testCanHandleRequestWithoutErrorWithNonMiddlewareInstance(): void
    {
        $responseMock      = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $serverRequestMock = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $middlewareMock    = new class implements MiddlewareInterface {
            public function process(ServerRequestInterface $serverRequest, RequestHandlerInterface $handler): ResponseInterface
            {
                return $handler->handle($serverRequest);
            }
        };
        $nonMiddlewareMock    = new class {
            public function process(ServerRequestInterface $serverRequest, RequestHandlerInterface $handler): ResponseInterface
            {
                return $handler->handle($serverRequest);
            }
        };

        $requestHandler = new RequestHandler($responseMock, [clone $middlewareMock, clone $nonMiddlewareMock]);

        /** @var ServerRequestInterface $serverRequestMock */
        $response = $requestHandler->handle($serverRequestMock);

        $this->assertEquals($responseMock, $response);
    }
}
