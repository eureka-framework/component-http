<?php declare(strict_types=1);

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http;

use Eureka\Component\Http\Message;
use Psr\Http\Message as HttpMessage;

/**
 * Class HttpFactory
 *
 * @author Romain Cottard
 */
class HttpFactory implements
    HttpMessage\RequestFactoryInterface,
    HttpMessage\ServerRequestFactoryInterface,
    HttpMessage\ResponseFactoryInterface,
    HttpMessage\StreamFactoryInterface,
    HttpMessage\UriFactoryInterface
{
    /**
     * Create a new response.
     *
     * @param int $code The HTTP status code. Defaults to 200.
     * @param string $reasonPhrase The reason phrase to associate with the status code
     *     in the generated response. If none is provided, implementations MAY use
     *     the defaults as suggested in the HTTP specification.
     * @return HttpMessage\ResponseInterface
     */
    public function createResponse(int $code = 200, string $reasonPhrase = ''): HttpMessage\ResponseInterface
    {
        return new Message\Response($code, [], $this->createStream(), '1.1', $reasonPhrase);
    }

    /**
     * Create a new request.
     *
     * @param string $method The HTTP method associated with the request.
     * @param HttpMessage\UriInterface|string $uri The URI associated with the request.
     * @return HttpMessage\RequestInterface
     */
    public function createRequest(string $method, $uri): HttpMessage\RequestInterface
    {
        if (!$uri instanceof HttpMessage\UriInterface) {
            $uri = $this->createUri(!is_string($uri) ? '' : $uri);
        }

        return new Message\Request($method, $uri);
    }

    /**
     * Create a new server request.
     *
     * Note that server parameters are taken precisely as given - no parsing/processing
     * of the given values is performed. In particular, no attempt is made to
     * determine the HTTP method or URI, which must be provided explicitly.
     *
     * @param string $method The HTTP method associated with the request.
     * @param HttpMessage\UriInterface|string $uri The URI associated with the request.
     * @param array $serverParams An array of Server API (SAPI) parameters with
     *     which to seed the generated request instance.
     * @return HttpMessage\ServerRequestInterface
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): HttpMessage\ServerRequestInterface
    {
        if (!$uri instanceof HttpMessage\UriInterface) {
            $uri = $this->createUri(!is_string($uri) ? '' : $uri);
        }

        $headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
        $body    = $this->createStream(file_get_contents('php://input'));
        $version = isset($serverParams['SERVER_PROTOCOL']) ? str_replace('HTTP/', '', $serverParams['SERVER_PROTOCOL']) : '1.1';

        //~ rewind body content
        $body->rewind();

        return (new Message\ServerRequest($method, $uri, $headers, $body, $version, $serverParams))
            ->withCookieParams($_COOKIE)
            ->withQueryParams($_GET)
            ->withParsedBody($_POST)
            ->withUploadedFiles($_FILES)
        ;
    }

    /**
     * Create a new URI.
     *
     * @param string $uri The URI to parse.
     * @return HttpMessage\UriInterface
     * @throws \InvalidArgumentException If the given URI cannot be parsed.
     */
    public function createUri(string $uri = '') : HttpMessage\UriInterface
    {
        //~ Create uri from string
        if (!empty($uri)) {
            return new Message\Uri($uri);
        }

        //~ Otherwise, create uri from server information.
        $instance = new Message\Uri();

        //~ Set scheme
        $instance = $instance->withScheme($_SERVER['REQUEST_SCHEME'] ?? 'http');

        //~ Set host
        if (isset($_SERVER['HTTP_HOST'])) {
            $instance = $instance->withHost($_SERVER['HTTP_HOST']);
        } elseif (isset($_SERVER['SERVER_NAME'])) {
            $instance = $instance->withHost($_SERVER['SERVER_NAME']);
        }

        //~ Set port
        if (isset($_SERVER['SERVER_PORT'])) {
            $instance = $instance->withPort($_SERVER['SERVER_PORT']);
        }

        //~ Set path
        if (isset($_SERVER['REQUEST_URI'])) {
            $instance = $instance->withPath(current(explode('?', $_SERVER['REQUEST_URI'])));
        }

        //~ Set query string
        if (isset($_SERVER['QUERY_STRING'])) {
            $instance = $instance->withQuery($_SERVER['QUERY_STRING']);
        }

        return $instance;
    }

    /**
     * Create a new stream from a string.
     *
     * The stream SHOULD be created with a temporary resource.
     *
     * @param string $content String content with which to populate the stream.
     * @return HttpMessage\StreamInterface
     */
    public function createStream(string $content = ''): HttpMessage\StreamInterface
    {
        $stream = new Message\Stream(fopen('php://temp', 'r+'));
        $stream->write($content);

        return $stream;
    }

    /**
     * Create a stream from an existing file.
     *
     * The file MUST be opened using the given mode, which may be any mode
     * supported by the `fopen` function.
     *
     * The `$filename` MAY be any string supported by `fopen()`.
     *
     * @param string $filename The filename or stream URI to use as basis of stream.
     * @param string $mode The mode with which to open the underlying filename/stream.
     * @return HttpMessage\StreamInterface
     * @throws \RuntimeException If the file cannot be opened.
     * @throws \InvalidArgumentException If the mode is invalid.
     */
    public function createStreamFromFile(string $filename, string $mode = 'r'): HttpMessage\StreamInterface
    {
        if (!file_exists($filename) || is_readable($filename)) {
            throw new \RuntimeException('File not exists or not readable');
        }

        $resource = fopen($filename, $mode);

        if ($resource === false) {
            throw new \InvalidArgumentException();
        }

        return (new Message\Stream($resource));
    }

    /**
     * Create a new stream from an existing resource.
     *
     * The stream MUST be readable and may be writable.
     *
     * @param resource $resource The PHP resource to use as the basis for the stream.
     * @return HttpMessage\StreamInterface
     */
    public function createStreamFromResource($resource): HttpMessage\StreamInterface
    {
        return new Message\Stream($resource);
    }
    /**
     * Create a new uploaded file.
     *
     * If a size is not provided it will be determined by checking the size of
     * the stream.
     *
     * @link http://php.net/manual/features.file-upload.post-method.php
     * @link http://php.net/manual/features.file-upload.errors.php
     *
     * @param HttpMessage\StreamInterface $stream The underlying stream representing the
     *     uploaded file content.
     * @param int $size The size of the file in bytes.
     * @param int $error The PHP file upload error.
     * @param string $clientFilename The filename as provided by the client, if any.
     * @param string $clientMediaType The media type as provided by the client, if any.
     * @return HttpMessage\UploadedFileInterface
     * @throws \InvalidArgumentException If the file resource is not readable.
     */
    public function createUploadedFile(
        HttpMessage\StreamInterface $stream,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): HttpMessage\UploadedFileInterface {

        return new Message\UploadedFile($stream, $clientFilename, $size, $clientMediaType, $error);
    }
}

