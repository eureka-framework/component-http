<?php

namespace Eureka\Component\Http\Message;

use Psr\Http\Message\ResponseInterface;

class ResponseSender
{
    /**
     * @var ResponseInterface response
     */
    private $response = null;

    /**
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
    public function send()
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
    private function writeStatus()
    {
        $string = $this->response->getProtocolVersion() . ' ' . $this->response->getStatusCode() . ' ' . $this->response->getReasonPhrase();
        header($string, true, $this->response->getStatusCode());
    }

    /**
     * Write headers.
     *
     * @return void
     */
    private function writeHeaders()
    {
        $headers = $this->response->getHeaders();
        foreach ($headers as $name => $list) {
            header("$name: " . implode(', ', $list));
        }
    }

    /**
     * @return void
     */
    private function writeBody()
    {
        echo $this->response->getBody()->getContents();
    }
}