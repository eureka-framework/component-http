<?php

/**
 * Copyright (c) 2010-2017 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http\Message;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class Request implements PSR-7 RequestInterface.
 *
 * @author  Romain Cottard
 * @link    http://www.php-fig.org/psr/psr-7/
 */
class ServerRequest extends Request implements ServerRequestInterface
{
    /**
     * @var array $serverParams
     */
    private $serverParams = [];

    /**
     * @var array $cookieParams
     */
    private $cookieParams = [];

    /**
     * @var array $queryParams
     */
    private $queryParams = [];

    /**
     * @var array $attributes
     */
    private $attributes = [];

    /**
     * @var array $parsedBody
     */
    private $parsedBody = [];

    /**
     * @var \Psr\Http\Message\UploadedFileInterface[] $uploadedFiles
     */
    private $uploadedFiles = [];

    /**
     * ServerRequest constructor.
     *
     * @param string          $method
     * @param UriInterface    $uri
     * @param array           $headers
     * @param StreamInterface $body
     * @param string          $protocolVersion
     * @param array           $serverParams
     */
    public function __construct($method, UriInterface $uri, array $headers = [], StreamInterface $body = null, $protocolVersion = '1.1', $serverParams = [])
    {
        parent::__construct($method, $uri, $headers, $body, $protocolVersion);

        $this->setServerParams($serverParams);
    }

    /**
     * {@inheritdoc}
     */
    public function getServerParams()
    {
        return $this->serverParams;
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieParams()
    {
        return $this->cookieParams;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * {@inheritdoc}
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * {@inheritdoc}
     */
    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($name, $default = null)
    {
        if (!array_key_exists($name, $this->attributes)) {
            return $default;
        }

        return $this->attributes[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function withCookieParams(array $cookies)
    {
        $instance = clone $this;
        $instance->cookieParams = $cookies;

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function withQueryParams(array $query)
    {
        $instance = clone $this;
        $instance->queryParams = $query;

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $instance = clone $this;
        $instance->uploadedFiles = $uploadedFiles;

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function withParsedBody($data)
    {
        $instance = clone $this;
        $instance->parsedBody = $data;

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function withAttribute($name, $value)
    {
        $instance = clone $this;
        $instance->attributes[$name] = $value;

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutAttribute($name)
    {
        $instance = clone $this;

        if (array_key_exists($name, $this->uploadedFiles)) {
            unset($instance->attributes[$name]);
        }

        return $instance;
    }

    /**
     * @return ServerRequestInterface
     */
    public static function createFromGlobal()
    {
        $method  = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
        $uri     = Uri::createFromGlobal();
        $body    = new Stream(Stream::createResourceTemp());
        $version = isset($_SERVER['SERVER_PROTOCOL']) ? str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL']) : '1.1';

        $instance = new ServerRequest($method, $uri, $headers, $body, $version, $_SERVER);
        $instance->setCookieParams($_COOKIE)
            ->setQueryParams($_GET)
            ->setParsedBody($_POST)
            ->setUploadedFiles($_FILES);

        return $instance;
    }

    /**
     * Set attributes.
     *
     * @param  array $attributes
     * @return self
     */
    private function setAttributes(array $attributes = [])
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Set cookie params.
     *
     * @param  array $cookieParams
     * @return self
     */
    private function setCookieParams(array $cookieParams = [])
    {
        $this->cookieParams = $cookieParams;

        return $this;
    }

    /**
     * Set query params.
     *
     * @param  array $queryParams
     * @return self
     */
    private function setQueryParams(array $queryParams = [])
    {
        $this->queryParams = $queryParams;

        return $this;
    }

    /**
     * Set Parsed body.
     *
     * @param  array $parsedBody
     * @return self
     */
    private function setParsedBody(array $parsedBody = [])
    {
        $this->parsedBody = $parsedBody;

        return $this;
    }

    /**
     * Set uploaded files.
     *
     * @param  array $uploadedFiles
     * @return self
     */
    private function setUploadedFiles(array $uploadedFiles = [])
    {
        $this->uploadedFiles = $uploadedFiles;

        return $this;
    }

    /**
     * Set Server params.
     *
     * @param  array $serverParams
     * @return self
     */
    private function setServerParams(array $serverParams = [])
    {
        $this->serverParams = $serverParams;

        return $this;
    }
}
