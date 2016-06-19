<?php

/**
 * Copyright (c) 2010-2016 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http;

/**
 * Cookies wrapper class.
 *
 * @author Romain Cottard
 * @version 2.1.0
 */
abstract class Data
{
    /**
     * Cookies data
     *
     * @var array $data
     */
    protected $data = array();

    /**
     * Data constructor.
     *
     * @return Data Current instance
     */
    abstract protected function __construct();

    /**
     * Singleton pattern method.
     *
     * @return Server|Env|Cookie|File|Get|Post|Session
     */
    public static function getInstance() {

        if (null === static::$instance) {
            $className = get_called_class();
            static::$instance = new $className();
        }

        return static::$instance;
    }

    /**
     * Get request data.
     *
     * @param    string $name
     * @param    mixed  $default
     * @return   mixed   Value
     * @throws   \Exception
     */
    public function get($name = null, $default = null)
    {
        $value = null;

        if (null === $name) {
            $value = $this->data;
        } elseif (isset($this->data[$name])) {
            $value = $this->data[$name];
        } elseif (null !== $default) {
            $value = $default;
        } else {
            throw new \Exception('Key not found in data ! (key: ' . $name . ')');
        }

        return $value;
    }

    /**
     * Check if request information exists
     *
     * @param    string $name
     * @return   boolean
     */
    public function has($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * Get request information.
     *
     * @param    string $name
     * @param    mixed  $value
     * @return   Post
     */
    public function set($name, $value)
    {
        $this->data[$name] = $value;

        return $this;
    }

}