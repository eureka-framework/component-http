<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Eureka\Component\Http\Server;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class RequestHandler
 *
 * @author Romain Cottard
 */
class RequestHandler implements RequestHandlerInterface
{
    /** @var \SplObjectStorage<MiddlewareInterface,null> */
    private \SplObjectStorage $storage;
    private ResponseInterface $response;

    /**
     * Class constructor.
     *
     * @param ResponseInterface $response
     * @param MiddlewareInterface[] $middleware
     */
    public function __construct(ResponseInterface $response, array $middleware = [])
    {
        $this->response = $response;
        $this->storage  = new \SplObjectStorage();

        foreach ($middleware as $item) {
            $this->storage->attach($item);
        }

        $this->storage->rewind();
    }

    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (0 === count($this->storage)) {
            return $this->response;
        }

        return $this->process($request);
    }

    /**
     * @param MiddlewareInterface $middleware
     * @return self
     */
    public function withMiddleware(MiddlewareInterface $middleware): self
    {
        $handler = clone $this;
        $handler->storage->detach($middleware);

        return $handler;
    }

    /**
     * @param MiddlewareInterface $middleware
     * @return self
     */
    protected function withoutMiddleware(MiddlewareInterface $middleware): self
    {
        $handler = clone $this;
        $handler->storage->detach($middleware);
        $handler->storage->rewind();

        return $handler;
    }

    /**
     * Process request through a middleware & return response.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    protected function process(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->storage->current();

        if (!($middleware instanceof MiddlewareInterface)) {
            return $this->response;
        }

        $stack = $this->withoutMiddleware($middleware);

        return $middleware->process($request, $stack);
    }

    /**
     * Clone current instance & sub-instance.
     */
    public function __clone()
    {
        $this->response = clone $this->response;
        $this->storage  = clone $this->storage;
    }
}
