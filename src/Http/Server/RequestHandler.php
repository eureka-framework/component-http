<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http\Server;

use Eureka\Psr\Http\Server\RequestHandlerInterface;
use Eureka\Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class RequestHandler
 *
 * @author Romain Cottard
 * @todo use real psr-15 when switch to PHP 7.1 will be effective
 */
class RequestHandler implements RequestHandlerInterface
{
    /** @var \SplObjectStorage $storage */
    private $storage = null;

    /** @var ResponseInterface $response */
    private $response = null;

    /**
     * Class constructor.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Eureka\Psr\Http\Server\MiddlewareInterface[] $middleware
     */
    public function __construct(ResponseInterface $response, $middleware = [])
    {
        $this->response = $response;
        $this->storage  = new \SplObjectStorage();

        foreach ($middleware as $item) {
            $this->storage->attach($item);
        }

        $this->storage->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ServerRequestInterface $request)
    {
        if (0 === count($this->storage)) {
            return $this->response;
        }

        return $this->process($request);
    }

    /**
     * @param \Eureka\Psr\Http\Server\MiddlewareInterface $middleware
     * @return \Eureka\Psr\Http\Server\RequestHandlerInterface
     */
    public function withMiddleware(MiddlewareInterface $middleware)
    {
        $handler = clone $this;
        $handler->storage->detach($middleware);

        return $handler;
    }

    /**
     * @param \Eureka\Psr\Http\Server\MiddlewareInterface $middleware
     * @return \Eureka\Psr\Http\Server\RequestHandlerInterface
     */
    public function withoutMiddleware(MiddlewareInterface $middleware)
    {
        $handler = clone $this;
        $handler->storage->detach($middleware);
        $handler->storage->rewind();

        return $handler;
    }

    /**
     * Process request through a middleware & return response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function process(ServerRequestInterface $request)
    {
        /** @var MiddlewareInterface $middleware */
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
