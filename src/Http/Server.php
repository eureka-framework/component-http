<?php

/**
 * Copyright (c) 2010-2016 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http;

/**
 * $_SERVER wrapper class.
 *
 * @author Romain Cottard
 */
class Server extends Data
{
    /**
     * @var Data $instance Current class instance.
     */
    protected static $instance = null;

    /**
     * Server constructor.
     */
    protected function __construct()
    {
        $this->data = $_SERVER;

        $this->init();
    }

    /**
     * Get current base Uri
     *
     * @return string Current base uri.
     */
    public function getBaseUri()
    {
        $script = $this->get('script');
        $base   = str_replace('\\', '/', dirname($script));

        if ($base != '/') {
            $url = strtolower($this->get('scheme')) . $this->get('name') . $base;
        } else {
            $url = strtolower($this->get('scheme')) . $this->get('name');
        }

        return $url;
    }

    /**
     * Get current uri.
     *
     * @param  bool $absolute
     * @return string
     */
    public function getCurrentUri($absolute = true)
    {
        if ($absolute) {
            return $this->get('scheme') . $this->get('name') . $this->get('uri');
        } else {
            return $this->get('uri');
        }
    }

    /**
     * Initialize Request data.
     *
     * @return void
     */
    public function init()
    {
        $this->data['host']     = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
        $this->data['script']   = (isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '');
        $this->data['uri']      = (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
        $this->data['query']    = (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '');
        $this->data['name']     = (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '');
        $this->data['referer']  = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
        $this->data['protocol'] = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        $this->data['scheme']   = (isset($_SERVER['HTTPS']) ? 'https://' : 'Http://');
        $this->data['is_post']  = (isset($_SERVER['REQUEST_METHOD']) ? 'POST' == $_SERVER['REQUEST_METHOD'] : false);
        $this->data['is_get']   = (isset($_SERVER['REQUEST_METHOD']) ? 'GET' == $_SERVER['REQUEST_METHOD'] : false);
        $this->data['is_ajax']  = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? true : false);

        if (isset($_SERVER['REMOTE_ADDR'])) {
            $this->data['ip'] = $_SERVER['REMOTE_ADDR'];
        } else {
            $this->data['ip'] = 'localhost';
        }
    }

    /**
     * If method is GET.
     *
     * @return bool
     */
    public function isGet()
    {
        return $this->data['is_get'];
    }

    /**
     * If method is POST.
     *
     * @return bool
     */
    public function isPost()
    {
        return $this->data['is_post'];
    }

    /**
     * If method is AJAX.
     *
     * @return bool
     */
    public function isAjax()
    {
        return $this->data['is_ajax'];
    }

    /**
     * Redirect on specified url.
     *
     * @param  string $url
     * @param  int    $status
     * @return void
     * @throws \Exception
     */
    public function redirect($url, $status = 301)
    {
        $status = (int) $status;

        if (!empty($url)) {

            header($this->get('protocol') . ' 301 Redirect');
            header('Status: ' . $status . ' Redirect');
            header('Location: ' . $url);
            exit(0);
        } else {
            throw new \Exception('Url is empty !');
        }
    }
}
