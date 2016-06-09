<?php

/**
 * Copyright (c) 2010-2016 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http;

/**
 * $_GET data wrapper class.
 *
 * @author Romain Cottard
 * @version 2.1.0
 */
class Get extends Data
{
    /**
     * Current class instance.
     *
     * @var Data $instance
     */
    protected static $instance = null;

    /**
     * Get constructor.
     *
     * @return Get Current instance
     */
    protected function __construct()
    {
        $this->data   = $_GET;
    }

}