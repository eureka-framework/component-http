<?php

/*
 * Copyright (c) Deezer
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http;

use Eureka\Component\Http\Message;
use Eureka\Component\Http\Message\Stream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class HttpFactory
 *
 * @author Jean Pasdeloup
 */
class HttpFactory
{
    /**
     * @param $uri
     * @return UriInterface
     */
    public static function createUri($uri)
    {
        return new Message\Uri($uri);
    }

    /**
     * @param $method
     * @param UriInterface $uri
     * @param array $headers
     * @param StreamInterface|null $body
     * @param string $protocolVersion
     * @return RequestInterface
     */
    public static function createRequest($method, UriInterface $uri, array $headers = [], StreamInterface $body = null, $protocolVersion = '1.1')
    {
        return new Message\Request($method, $uri, $headers, $body, $protocolVersion);
    }

    /**
     * @param  string $uri
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public static function createServerRequest($uri = '')
    {
        $method  = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $headers = self::getHeaders();
        $uri     = self::createUri($_SERVER['REQUEST_URI']);
        $body    = new Message\Stream(fopen('php://input', 'r+'));
        $version = isset($_SERVER['SERVER_PROTOCOL']) ? str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL']) : '1.1';

        $instance = new Message\ServerRequest($method, $uri, $headers, $body, $version, $_SERVER);
        $instance = $instance->withCookieParams($_COOKIE)
            ->withQueryParams($_GET)
            ->withParsedBody(self::getParsedBody())
            ->withUploadedFiles($_FILES)
        ;

        return $instance;
    }

    /**
     * @param int $status
     * @param array $headers
     * @param StreamInterface|null $body
     * @param string $version
     * @param null $reason
     * @return ResponseInterface
     */
    public static function createResponse($status = 200, array $headers = [], StreamInterface $body = null, $version = '1.1', $reason = null)
    {
        return new Message\Response($status, $headers, $body, $version, $reason);
    }

    /**
     * Create Stream object from php://temp and write a string in it
     *
     * @param string $mode
     * @param bool $rewind
     * @param string $stringToWrite
     * @return Stream
     */
    public static function createStreamFromString($stringToWrite, $rewind = false, $mode = 'r+')
    {
        $resource = Stream::createResourceTemp($mode);

        $stream = new Stream($resource);

        $stream->write($stringToWrite);

        if ($rewind) {
            $stream->rewind();
        }

        return $stream;
    }

    /**
     * @return array
     */
    private static function getHeaders()
    {
        $headers = function_exists('apache_request_headers') ? apache_request_headers() : [];

        if ($headers === false) {
            $headers = [];
        }

        foreach ($headers as $name => $header) {
            if (!is_array($header)) {
                $header = [$header];
            }

            $headers[$name] = $header;
        }

        return $headers;
    }

    /**
     * @return array|bool|mixed|string
     */
    private static function getParsedBody()
    {
        $requestBody = file_get_contents('php://input', 'r');
        $parsedBody  = !empty($requestBody) ? json_decode($requestBody, true) : [];

        if (!empty($requestBody) && empty($parsedBody)) {
            parse_str($requestBody, $parsedBody);
        }

        return $parsedBody;
    }
}
