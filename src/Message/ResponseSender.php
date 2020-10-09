<?php declare(strict_types=1);

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http\Message;

use Psr\Http\Message\ResponseInterface;

/**
 * Class ResponseSender
 *
 * @author  Romain Cottard
 */
class ResponseSender
{
    /** @var ResponseInterface $response */
    private $response = null;

    /**
     * ResponseSender constructor.
     *
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Send response to the client.
     *
     * @return void
     */
    public function send(): void
    {
        $this->writeStatus();
        $this->writeHeaders();
        $this->writeBody();
    }

    /**
     * Write response status
     *
     * @return void
     */
    private function writeStatus(): void
    {
        $string = $this->response->getProtocolVersion() . ' ' . $this->response->getStatusCode() . ' ' . $this->response->getReasonPhrase();
        header($string, true, $this->response->getStatusCode());
    }

    /**
     * Write headers.
     *
     * @return void
     */
    private function writeHeaders(): void
    {
        $headers = $this->response->getHeaders();
        foreach ($headers as $name => $list) {
            header("$name: " . implode(', ', $list));
        }
    }

    /**
     * @return void
     */
    private function writeBody(): void
    {
        echo $this->response->getBody()->getContents();
    }
}
