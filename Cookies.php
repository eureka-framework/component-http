<?php

/**
 * Copyright (c) 2010-2016 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http;

/**
 * $_COOKIE wrapper class.
 *
 * @author Romain Cottard
 * @version 2.1.0
 */
class Cookie extends Data
{

    /**
     * Server instance
     *
     * @var Server $server
     */
    protected $server = null;

    /**
     * Cookie constructor.
     *
     * @return Cookie
     */
    protected function __construct()
    {
        $this->data = $_COOKIE;
    }

    /**
     * Get cookie information.
     *
     * @param    string $name
     * @param    mixed  $default
     * @return   mixed   Cookie data.
     */
    public function get($name, $default = null)
    {
        return unserialize(parent::get($name, $default));
    }

    /**
     * Set cookie information.
     *
     * @param    string  $name Cookie Name
     * @param    mixed   $value Cookie Value
     * @param    integer $time Cookie Lifetime.
     * @return   void
     */
    public function set($name, $value = null, $time = 2592000)
    {
        $value = serialize($value);
        parent::set($name, $value);

        setcookie($name, $value, time() + $time, '/', $this->server->get('name'));
    }

}