<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Eureka\Component\Http\Tests\Unit;

use Eureka\Component\Http\Server\RequestHandler;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
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
     *@throws Exception
*/
    public function testCanInstantiateRequestHandler(): void
    {
        $responseMock   = $this->createMock(ResponseInterface::class);
        $requestHandler = new RequestHandler($responseMock);

        self::assertInstanceOf(RequestHandlerInterface::class, $requestHandler);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testCanHandleRequest(): void
    {
        $responseMock      = $this->createMock(ResponseInterface::class);
        $serverRequestMock = $this->createMock(ServerRequestInterface::class);
        $middlewareMock    = new class implements MiddlewareInterface {
            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {
                return $handler->handle($request);
            }
        };

        $requestHandler = new RequestHandler($responseMock, [clone $middlewareMock]);
        $requestHandler = $requestHandler->withMiddleware(clone $middlewareMock);

        /** @var ServerRequestInterface&MockObject $serverRequestMock */
        $response = $requestHandler->handle($serverRequestMock);

        self::assertEquals($responseMock, $response);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testCanHandleRequestWithoutErrorWithNonMiddlewareInstance(): void
    {
        $responseMock      = $this->createMock(ResponseInterface::class);
        $serverRequestMock = $this->createMock(ServerRequestInterface::class);
        $middlewareMock    = new class implements MiddlewareInterface {
            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {
                return $handler->handle($request);
            }
        };
        $nonMiddlewareMock = new class {
            public function process(ServerRequestInterface $serverRequest, RequestHandlerInterface $handler): ResponseInterface
            {
                return $handler->handle($serverRequest);
            }
        };

        $requestHandler = new RequestHandler($responseMock, [clone $middlewareMock, clone $nonMiddlewareMock]);

        /** @var ServerRequestInterface&MockObject $serverRequestMock */
        $response = $requestHandler->handle($serverRequestMock);

        self::assertEquals($responseMock, $response);
    }
}
