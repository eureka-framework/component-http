<?php

/**
 * Copyright (c) 2010-2017 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http;

/**
 * $_COOKIE wrapper class.
 *
 * @author Romain Cottard
 */
class Cookie extends Data
{
    /**
     * @var Server $server Server instance
     */
    protected $server = null;

    /**
     * Cookie constructor.
     */
    protected function __construct()
    {
        $this->data = $_COOKIE;
    }

    /**
     * Get cookie information.
     *
     * @param  string $name
     * @param  mixed  $default
     * @return mixed   Cookie data.
     */
    public function get($name, $default = null)
    {
        return json_decode(parent::get($name, $default));
    }

    /**
     * Set cookie information.
     *
     * @param  string $name Cookie Name
     * @param  mixed  $value Cookie Value
     * @param  int    $time Cookie Lifetime.
     * @return void
     */
    public function set($name, $value = null, $time = 2592000)
    {
        $value = json_encode($value);
        parent::set($name, $value);

        setcookie($name, $value, time() + $time, '/', $this->server->get('name'));
    }
}