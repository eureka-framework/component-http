<?php

/**
 * Copyright (c) 2010-2017 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Eureka\Component\Psr\Http\Middleware\StackInterface;
use Eureka\Component\Psr\Http\Middleware\DelegateInterface;
use Eureka\Component\Psr\Http\Middleware\MiddlewareInterface;

class Stack implements StackInterface, DelegateInterface
{
    /**
     * @var \SplObjectStorage $storage
     */
    private $storage = null;

    /**
     * @var ResponseInterface $response
     */
    private $response = null;

    /**
     * Class constructor.
     *
     * @param ResponseInterface $response
     * @param MiddlewareInterface[] $middleware
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
    public function withMiddleware(MiddlewareInterface $middleware)
    {
        $stack = clone $this;
        $stack->storage->detach($middleware);

        return $stack;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutMiddleware(MiddlewareInterface $middleware)
    {
        $stack = clone $this;
        $stack->storage->detach($middleware);
        $stack->storage->rewind();

        return $stack;
    }

    /**
     * {@inheritdoc}
     */
    public function next(RequestInterface $request)
    {
        if (0 === count($this->storage)) {
            return $this->response;
        }

        return $this->process($request);
    }

    /**
     * {@inheritdoc}
     */
    public function process(RequestInterface $request)
    {
        /** @var ServerMiddlewareInterface|ClientMiddlewareInterface $middleware */
        $middleware = $this->storage->current();

        if (!($middleware instanceof MiddlewareInterface)) {
            return $this->response;
        }

        $stack = $this->withoutMiddleware($middleware);

        return $middleware->process($request, $stack);
    }

    /**
     * {@inheritdoc}
     */
    public function __clone()
    {
        $this->response = clone $this->response;
        $this->storage  = clone $this->storage;
    }
}