<?php

/**
 * Copyright (c) 2010-2016 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http;

/**
 * Class to manipulate http query
 *
 * @author Romain Cottard
 */
class Http
{
    /**
     * @var array $url Url data
     */
    protected $url = array();

    /**
     * Class constructor.
     *
     * @param    string $url Url to parse. If null, get current url.
     */
    public function __construct($url = null)
    {
        $this->init($url);
    }

    /**
     * Add, replace or remove parameters from query url.
     *
     * @param  mixed $name Array of parameter, or parameter name.
     * @param  mixed $value Null if first parameter is array, else parameter value.
     * @return self
     */
    public function add($name, $value = null)
    {
        if (empty($name)) {
            return $this;
        }

        $params = (!is_array($name) ? array($name => $value) : $name);

        $query = $this->query(true);

        foreach ($params as $key => $val) {
            if (empty($val)) {
                unset($query[$key]);
            } elseif (!empty($val)) {
                $query[$key] = $val;
            }
        }

        $query              = http_build_query($query);
        $this->url['query'] = $query;

        return $this;
    }

    /**
     * Initialize object properties.
     *
     * @param  string $url Url to parse. If null, get current url.
     * @return self
     */
    public function init($url = null)
    {
        $url       = (empty($url) ? Server::getInstance()->getCurrentUri() : $url);
        $this->url = parse_url($url);

        return $this;
    }

    /**
     * Return query url.
     *
     * @param  bool $array If return array or string
     * @return mixed   Query Url
     */
    public function query($array = false)
    {
        $query = (!empty($this->url['query']) ? $this->url['query'] : '');

        if ($array) {
            parse_str($query, $query);

            return $query;
        } else {
            return $query;
        }
    }

    /**
     * Add or replace path
     *
     * @param  string $path
     * @param  string $type
     * @param  string $replace
     * @return self
     */
    public function setPath($path, $type = 'replace', $replace = '')
    {
        $oldPath = (!empty($this->url['path']) ? $this->url['path'] : '');

        switch ($type) {
            case 'add':
                $path = $oldPath . $path;
                break;
            case 'remove':
                $path = str_replace($path, '', $oldPath);
                break;
            case 'replace':
            default:
                $path = str_replace($replace, $path, $oldPath);
                break;
        }

        $this->url['path'] = $path;

        return $this;
    }

    /**
     * Get formatted uri.
     *
     * @return string Current uri.
     */
    public function uri()
    {
        $uri = (!empty($this->url['scheme']) ? $this->url['scheme'] . '://' : Server::getInstance()->get('scheme', ''));
        $uri .= (!empty($this->url['user']) ? $this->url['user'] . ':' . $this->url['pass'] . '@' : '');
        $uri .= (!empty($this->url['host']) ? $this->url['host'] : Server::getInstance()->get('host', ''));
        $uri .= (!empty($this->url['port']) ? ':' . $this->url['port'] : '');
        $uri .= (!empty($this->url['path']) ? $this->url['path'] : '');
        $uri .= (!empty($this->url['query']) ? '?' . $this->url['query'] : '');
        $uri .= (!empty($this->url['fragment']) ? '#' . $this->url['fragment'] : '');

        return $uri;
    }
}