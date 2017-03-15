<?php

/**
 * Copyright (c) 2010-2017 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http\Message;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Representation of an outgoing, server-side response.
 *
 * Per the HTTP specification, this interface includes properties for
 * each of the following:
 *
 * - Protocol version
 * - Status code and reason phrase
 * - Headers
 * - Message body
 *
 * Responses are considered immutable; all methods that might change state MUST
 * be implemented such that they retain the internal state of the current
 * message and return an instance that contains the changed state.
 *
 * @author  Romain Cottard
 * @link    http://www.php-fig.org/psr/psr-7/
 */
class Response implements ResponseInterface
{
    use MessageTrait;

    /**
     * @var string[] $reasonPhrases List of http code with label
     */
    protected static $reasonPhrases = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',

        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',

        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',

        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot', // Easter Egg :)
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',

        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    );

    /**
     * @var string $reasonPhrase Reason phrase associated with status code.
     */
    private $reasonPhrase = '';

    /**
     * @var int $statusCode status code
     */
    private $statusCode = 200;

    /**
     * @param int    $status  Status code for the response, if any.
     * @param array  $headers Headers for the response, if any.
     * @param StreamInterface  $body    Stream body.
     * @param string $version Protocol version.
     * @param string $reason  Reason phrase (a default will be used if possible).
     */
    public function __construct($status = 200, array $headers = [], StreamInterface $body = null, $version = '1.1', $reason = null)
    {
        $this->setHeaders($headers);
        $this->setBody($body);
        $this->setProtocolVersion($version);

        $this->statusCode = (int) $status;

        if (empty($reason) && isset(self::$reasonPhrases[$this->statusCode])) {
            $this->reasonPhrase = self::$reasonPhrases[$status];
        } else {
            $this->reasonPhrase = (string) $reason;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /**
     * {@inheritdoc}
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $instance = clone $this;
        $instance->statusCode = (int) $code;

        //~ Look for default reason phrase
        if (empty($reasonPhrase) && isset(self::$reasonPhrases[$instance->statusCode])) {
            $reasonPhrase = self::$reasonPhrases[$instance->statusCode];
        }

        $instance->reasonPhrase = (string) $reasonPhrase;

        return $instance;
    }
}
