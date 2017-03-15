<?php

/**
 * Copyright (c) 2010-2017 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http;

/**
 * Cookies wrapper class.
 *
 * @author Romain Cottard
 */
abstract class Data
{
    /**
     * @var array $data $_{VARNAME} data.
     */
    protected $data = array();

    /**
     * Data constructor.
     */
    abstract protected function __construct();

    /**
     * Singleton pattern method.
     *
     * @return Server|Env|Cookie|File|Get|Post|Session
     */
    public static function getInstance()
    {

        if (null === static::$instance) {
            $className        = get_called_class();
            static::$instance = new $className();
        }

        return static::$instance;
    }

    /**
     * Get request data.
     *
     * @param  string $name
     * @param  mixed  $default
     * @return mixed   Value
     * @throws \Exception
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
     * @param  string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * Get request information.
     *
     * @param  string $name
     * @param  mixed  $value
     * @return static
     */
    public function set($name, $value)
    {
        $this->data[$name] = $value;

        return $this;
    }
}