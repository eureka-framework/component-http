<?php

/**
 * Copyright (c) 2010-2016 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http\Message;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class Request implements PSR-7 RequestInterface.
 *
 * @author  Romain Cottard <rco@deezer.com>
 * @link    http://www.php-fig.org/psr/psr-7/
 */
class Request implements RequestInterface
{
    use MessageTrait;

    /**
     * @var string $requestTarget the message's request target.
     */
    private $requestTarget = '';

    /**
     * @var string $method the HTTP method of the request.
     */
    private $method = null;

    /**
     * @var UriInterface $uri Request URI to use.
     */
    private $uri = '';

    /**
     * @param string $method HTTP method for the request.
     * @param UriInterface $uri URI for the request.
     * @param array $headers Headers for the message.
     * @param StreamInterface $body Message body.
     * @param string $protocolVersion HTTP protocol version.
     *
     * @throws \InvalidArgumentException for an invalid URI
     */
    public function __construct($method, UriInterface $uri, array $headers = [], StreamInterface $body = null, $protocolVersion = '1.1')
    {
        $this->setHeaders($headers);
        $this->setBody($body);
        $this->setProtocolVersion($protocolVersion);

        if (!empty($uri->getHost()) && !$this->hasHeader('Host')) {
            $this->replaceHostHeader($uri);
        }

        $this->method = strtoupper($method);
        $this->uri    = $uri;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestTarget()
    {
        if (!empty($this->requestTarget)) {
            return $this->requestTarget;
        }

        $target = $this->uri->getPath();
        if (empty($target)) {
            $target = '/';
        }

        if (!empty($this->uri->getQuery())) {
            $target .= '?' . $this->uri->getQuery();
        }

        return $target;
    }

    /**
     * {@inheritdoc}
     */
    public function withRequestTarget($requestTarget)
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new \InvalidArgumentException('Invalid request target provided; cannot contain whitespace');
        }

        $instance = clone $this;
        $instance->requestTarget = $requestTarget;

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function withMethod($method)
    {
        $instance = clone $this;
        $instance->method = $method;

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * {@inheritdoc}
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $instance = clone $this;
        $instance->uri = $uri;

        if (!$preserveHost && !empty($uri->getHost())) {
            $instance->replaceHostHeader($uri);
        }

        return $instance;
    }

    /**
     * Build Request from a server Request.
     *
     * @param  ServerRequest $serverRequest
     * @return Request
     */
    public static function createFromServerRequest(ServerRequest $serverRequest)
    {
        return new Request(
            $serverRequest->getMethod(),
            $serverRequest->getUri(),
            $serverRequest->getHeaders(),
            $serverRequest->getBody(),
            $serverRequest->getProtocolVersion()
        );
    }

    /**
     * Update host header if host is not empty.
     *
     * @param  UriInterface $uri
     * @return void
     */
    private function replaceHostHeader(UriInterface $uri)
    {
        $host = $uri->getHost();

        if (!empty($host)) {

            $port = $uri->getPort();
            if (!empty($port)) {
                $host .= ':' . $port;
            }

            //~ Ensure Host is the first header. See: http://tools.ietf.org/html/rfc7230#section-5.4
            $this->headersOriginal = ['Host' => [$host]] + $this->headersOriginal;
            $this->headers         = ['host' => [$host]] + $this->headers;
        }
    }
}
