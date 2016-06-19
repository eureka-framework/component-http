<?php

/**
 * Copyright (c) 2010-2016 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http;

/**
 * $_ENV wrapper class.
 *
 * @author Romain Cottard
 * @version 2.1.0
 */
class Env extends Data
{
    /**
     * Current class instance.
     *
     * @var Data $instance
     */
    protected static $instance = null;

    /**
     * Env constructor.
     *
     * @return Env Current instance
     */
    protected function __construct()
    {
        $this->data = $_ENV;
    }

}