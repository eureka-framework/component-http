<?php

/**
 * Copyright (c) 2010-2017 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http;

/**
 * $_ENV wrapper class.
 *
 * @author Romain Cottard
 */
class Env extends Data
{
    /**
     * @var Data $instance Current class instance.
     */
    protected static $instance = null;

    /**
     * Env constructor.
     */
    protected function __construct()
    {
        $this->data = $_ENV;
    }
}