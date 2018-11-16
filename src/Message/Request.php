<?php

/*
 * Copyright (c) Romain Cottard
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
 * @author  Romain Cottard
 * @link    http://www.php-fig.org/psr/psr-7/
 */
class Request implements RequestInterface
{
    use MessageTrait;

    /** @var string $requestTarget the message's request target. */
    private $requestTarget = '';

    /** @var string $method the HTTP method of the request. */
    private $method = null;

    /** @var UriInterface $uri Request URI to use. */
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
    public function __construct(
        string $method,
        UriInterface $uri,
        array $headers = [],
        StreamInterface $body = null,
        string $protocolVersion = '1.1'
    ) {
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
     * Retrieves the message's request target.
     *
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     *
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     *
     * If no URI is available, and no request-target has been specifically
     * provided, this method MUST return the string "/".
     *
     * @return string
     */
    public function getRequestTarget(): string
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
     * Return an instance with the specific request-target.
     *
     * If the request needs a non-origin-form request-target — e.g., for
     * specifying an absolute-form, authority-form, or asterisk-form —
     * this method may be used to create an instance with the specified
     * request-target, verbatim.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request target.
     *
     * @link http://tools.ietf.org/html/rfc7230#section-5.3 (for the various
     *     request-target forms allowed in request messages)
     * @param string $requestTarget
     * @return static
     */
    public function withRequestTarget($requestTarget): self
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new \InvalidArgumentException('Invalid request target provided; cannot contain whitespace');
        }

        $instance                = clone $this;
        $instance->requestTarget = $requestTarget;

        return $instance;
    }

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Return an instance with the provided HTTP method.
     *
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request method.
     *
     * @param string $method Case-sensitive method.
     * @return static
     * @throws \InvalidArgumentException for invalid HTTP methods.
     */
    public function withMethod($method): self
    {
        $instance         = clone $this;
        $instance->method = $method;

        return $instance;
    }

    /**
     * Retrieves the URI instance.
     *
     * This method MUST return a UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @return UriInterface Returns a UriInterface instance
     *     representing the URI of the request.
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * Returns an instance with the provided URI.
     *
     * This method MUST update the Host header of the returned request by
     * default if the URI contains a host component. If the URI does not
     * contain a host component, any pre-existing Host header MUST be carried
     * over to the returned request.
     *
     * You can opt-in to preserving the original state of the Host header by
     * setting `$preserveHost` to `true`. When `$preserveHost` is set to
     * `true`, this method interacts with the Host header in the following ways:
     *
     * - If the Host header is missing or empty, and the new URI contains
     *   a host component, this method MUST update the Host header in the returned
     *   request.
     * - If the Host header is missing or empty, and the new URI does not contain a
     *   host component, this method MUST NOT update the Host header in the returned
     *   request.
     * - If a Host header is present and non-empty, this method MUST NOT update
     *   the Host header in the returned request.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @param UriInterface $uri New request URI to use.
     * @param bool $preserveHost Preserve the original state of the Host header.
     * @return static
     */
    public function withUri(UriInterface $uri, $preserveHost = false): self
    {
        $instance      = clone $this;
        $instance->uri = $uri;

        if (!$preserveHost && !empty($uri->getHost())) {
            $instance->replaceHostHeader($uri);
        }

        return $instance;
    }

    /**
     * Update host header if host is not empty.
     *
     * @param  UriInterface $uri
     * @return void
     */
    private function replaceHostHeader(UriInterface $uri): void
    {
        $host = $uri->getHost();

        if (!empty($host)) {

            $port = $uri->getPort();
            if (!empty($port)) {
                $host .= ':' . $port;
            }

            //~ Ensure Host is the first header. See: http://tools.ietf.org/html/rfc7230#section-5.4
            $this->addHeaders(['Host' => [$host]]);
            $this->addHeadersOriginal(['Host' => [$host]]);
        }
    }
}
