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
use PHPUnit\Framework\MockObject\Stub;
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
     * @throws Exception
     */
    public function testCanHandleRequest(): void
    {
        $responseStub      = self::createStub(ResponseInterface::class);
        $serverRequestStub = self::createStub(ServerRequestInterface::class);
        $middlewareMock    = new class implements MiddlewareInterface {
            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {
                return $handler->handle($request);
            }
        };

        $requestHandler = new RequestHandler($responseStub, [clone $middlewareMock]);

        $response = $requestHandler->handle($serverRequestStub);

        self::assertEquals($responseStub, $response);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testCanHandleRequestWithoutErrorWithNonMiddlewareInstance(): void
    {
        $responseStub      = self::createStub(ResponseInterface::class);
        $serverRequestStub = self::createStub(ServerRequestInterface::class);
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

        $requestHandler = new RequestHandler($responseStub, [clone $middlewareMock, clone $nonMiddlewareMock]);

        $response = $requestHandler->handle($serverRequestStub);

        self::assertEquals($responseStub, $response);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testCanHandleRequestWithGivenMiddleware(): void
    {
        $responseStub      = self::createStub(ResponseInterface::class);
        $serverRequestStub = self::createStub(ServerRequestInterface::class);
        $middlewareMock    = new class implements MiddlewareInterface {
            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {
                /** @var Stub&ResponseInterface $responseStub */
                $responseStub = $handler->handle($request);
                $responseStub->method('getStatusCode')->willReturn(200);
                return $responseStub;
            }
        };

        $requestHandler = new RequestHandler($responseStub);
        $requestHandler = $requestHandler->withMiddleware(clone $middlewareMock);

        $response = $requestHandler->handle($serverRequestStub);

        self::assertSame(200, $response->getStatusCode());
    }
}
