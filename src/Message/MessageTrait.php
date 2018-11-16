<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http\Message;

use Psr\Http\Message\StreamInterface;

/**
 * Trait for message implementation of PSR-7 message interface.
 *
 * HTTP messages consist of requests from a client to a server and responses
 * from a server to a client. This interface defines the methods common to
 * each.
 *
 * Messages are considered immutable; all methods that might change state MUST
 * be implemented such that they retain the internal state of the current
 * message and return an instance that contains the changed state.
 *
 *
 * @author  Romain Cottard
 * @link    http://www.php-fig.org/psr/psr-7/
 * @see http://www.ietf.org/rfc/rfc7230.txt
 * @see http://www.ietf.org/rfc/rfc7231.txt
 */
trait MessageTrait
{
    /** @var string[][] $headers an associative array of the message's headers. Each key MUST be a header name, and each value MUST be an array of strings for that header. */
    private $headers = [];

    /** @var string $protocolVersion HTTP protocol version. */
    private $protocolVersion = '1.1';

    /** @var StreamInterface $body StreamInterface instance for body. */
    private $body = null;

    /** @var string[][] $headers an associative array of the message's headers. Each key MUST be a header name, and each value MUST be an array of strings for that header. */
    protected $headersOriginal = [];

    /**
     * Retrieves the HTTP protocol version as a string.
     *
     * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
     *
     * @return string HTTP protocol version.
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * Return an instance with the specified HTTP protocol version.
     *
     * The version string MUST contain only the HTTP version number (e.g.,
     * "1.1", "1.0").
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new protocol version.
     *
     * @param string $version HTTP protocol version
     * @return static
     */
    public function withProtocolVersion($version): self
    {
        $instance                  = clone $this;
        $instance->protocolVersion = $version;

        return $instance;
    }

    /**
     * Retrieves all message header values.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     *     // Represent the headers as a string
     *     foreach ($message->getHeaders() as $name => $values) {
     *         echo $name . ': ' . implode(', ', $values);
     *     }
     *
     *     // Emit headers iteratively:
     *     foreach ($message->getHeaders() as $name => $values) {
     *         foreach ($values as $value) {
     *             header(sprintf('%s: %s', $name, $value), false);
     *         }
     *     }
     *
     * While header names are not case-sensitive, getHeaders() will preserve the
     * exact case in which headers were originally specified.
     *
     * @return string[][] Returns an associative array of the message's headers.
     *     Each key MUST be a header name, and each value MUST be an array of
     *     strings for that header.
     */
    public function getHeaders(): array
    {
        return $this->headersOriginal;
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function hasHeader($name): bool
    {
        return isset($this->headers[strtolower($name)]);
    }

    /**
     * Retrieves a message header value by the given case-insensitive name.
     *
     * This method returns an array of all the header values of the given
     * case-insensitive header name.
     *
     * If the header does not appear in the message, this method MUST return an
     * empty array.
     *
     * @param string $name Case-insensitive header field name.
     * @return string[] An array of string values as provided for the given
     *    header. If the header does not appear in the message, this method MUST
     *    return an empty array.
     */
    public function getHeader($name): array
    {
        return ($this->hasHeader($name) ? $this->headers[strtolower($name)] : []);
    }

    /**
     * Retrieves a comma-separated string of the values for a single header.
     *
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma.
     *
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation. For such headers, use getHeader() instead
     * and supply your own delimiter when concatenating.
     *
     * If the header does not appear in the message, this method MUST return
     * an empty string.
     *
     * @param string $name Case-insensitive header field name.
     * @return string A string of values as provided for the given header
     *    concatenated together using a comma. If the header does not appear in
     *    the message, this method MUST return an empty string.
     */
    public function getHeaderLine($name): string
    {
        return implode(', ', $this->getHeader($name));
    }

    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * While header names are case-insensitive, the casing of the header will
     * be preserved by this function, and returned from getHeaders().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated header and value.
     *
     * @param  string $name Case-insensitive header field name.
     * @param  string|string[] $value Header value(s).
     * @return static
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withHeader($name, $value): self
    {
        //~ Convert to array
        if (!is_array($value)) {
            $value = [$value];
        }

        //~ Cleaning values
        foreach ($value as $key => $val) {
            $value[$key] = trim($val);
        }

        $instance                             = clone $this;
        $instance->headers[strtolower($name)] = $value;
        $instance->headersOriginal[$name]     = $value;

        return $instance;
    }

    /**
     * Return an instance with the specified header appended with the given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new header and/or value.
     *
     * @param  string $name Case-insensitive header field name to add.
     * @param  string|string[] $value Header value(s).
     * @return static
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withAddedHeader($name, $value): self
    {
        $instance = clone $this;

        //~ Init header if necessary
        if (!$instance->hasHeader($name)) {
            $instance->headers[strtolower($name)] = [];
            $instance->headersOriginal[$name]     = [];
        }

        //~ Convert to array
        if (!is_array($value)) {
            $value = [$value];
        }

        //~ Cleaning values & set
        foreach ($value as $val) {
            $val                                    = trim($val);
            $instance->headers[strtolower($name)][] = $val;
            $instance->headersOriginal[$name][]     = $val;
        }

        return $instance;
    }

    /**
     * Return an instance without the specified header.
     *
     * Header resolution MUST be done without case-sensitivity.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the named header.
     *
     * @param  string $name Case-insensitive header field name to remove.
     * @return static
     */
    public function withoutHeader($name): self
    {
        $instance = clone $this;

        if ($instance->hasHeader($name)) {
            unset($instance->headers[strtolower($name)]);
            unset($instance->headersOriginal[$name]);
        }

        return $instance;
    }

    /**
     * Gets the body of the message.
     *
     * @return StreamInterface Returns the body as a stream.
     */
    public function getBody(): StreamInterface
    {
        if (!($this->body instanceof StreamInterface)) {
            $this->body = (new HttpFactory())->createStream();
        }

        return $this->body;
    }

    /**
     * Return an instance with the specified message body.
     *
     * The body MUST be a StreamInterface object.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new body stream.
     *
     * @param  StreamInterface $body Body.
     * @return static
     * @throws \InvalidArgumentException When the body is not valid.
     */
    public function withBody(StreamInterface $body): self
    {
        $instance       = clone $this;
        $instance->body = $body;

        return $instance;
    }

    /**
     * Set headers
     *
     * @param  string[][] $headers
     * @return self
     */
    protected function setHeaders(array $headers): self
    {
        foreach ($headers as $name => $value) {

            //~ Convert to array
            if (!is_array($value)) {
                $value = [$value];
            }

            //~ Clean values
            foreach ($value as $key => $val) {
                $value[$key] = trim($val);
            }

            $this->headersOriginal[$name]     = $value;
            $this->headers[strtolower($name)] = $value;
        }

        return $this;
    }

    /**
     * Set Body
     *
     * @param  StreamInterface $body
     * @return static
     */
    protected function setBody(StreamInterface $body = null): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Set protocol version.
     *
     * @param  string $version Protocol version
     * @return static
     */
    protected function setProtocolVersion($version): self
    {
        $this->protocolVersion = $version;

        return $this;
    }

    /**
     * @param array $headers
     * @return static
     */
    protected function addHeaders(array $headers): self
    {
        $this->headers += $headers;

        return $this;
    }

    /**
     * @param array $headers
     * @return static
     */
    protected function addHeadersOriginal(array $headers): self
    {
        $this->headersOriginal += $headers;

        return $this;
    }
}
