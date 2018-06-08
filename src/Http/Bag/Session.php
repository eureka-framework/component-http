<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http\Bag;

use Eureka\Interfaces\Bag\BagInterface;
use Eureka\Interfaces\Bag\BagTrait;

/**
 * $_SESSION data wrapper class.
 *
 * @author Romain Cottard
 */
class Session implements BagInterface
{
    use BagTrait;

    /** @var string EPHEMERAL Session index name for ephemeral var in Session. */
    const EPHEMERAL = 'eureka-ephemeral';

    /** @var string ACTIVE Session index name for ephemeral var if active or not. */
    const ACTIVE = 'active';

    /** @var string VARIABLE Session index name for ephemeral var content. */
    const VARIABLE = 'var';

    /** @var \Eureka\Component\Http\Bag\Session $instance Current class instance. */
    protected static $instance = null;


    /**
     * Session constructor.
     */
    private function __construct()
    {
        $this->bag = &$_SESSION;

        $this->clearEphemeral();
    }

    /**
     * Singleton pattern method.
     *
     * @return \Eureka\Component\Http\Bag\Session
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * Get Session ephemeral variable specified.
     *
     * @param  string $name
     * @return mixed  Variable value.
     */
    public function getEphemeral($name)
    {
        $ephemeral = $this->get(self::EPHEMERAL);
        if (isset($ephemeral[$name][self::VARIABLE])) {
            return $ephemeral[$name][self::VARIABLE];
        } else {
            return null;
        }
    }

    /**
     * Check if have specified ephemeral var in Session.
     *
     * @param  string $name Index Session name.
     * @return bool
     */
    public function hasEphemeral($name)
    {
        $ephemeral = $this->get(self::EPHEMERAL);

        return isset($ephemeral[$name]);
    }

    /**
     * Initialize Session. Remove old ephemeral var in Session.
     *
     * @return $this
     */
    public function clearEphemeral()
    {
        //~ Check ephemeral vars
        if ($this->has(self::EPHEMERAL)) {
            $ephemeral = $this->get(self::EPHEMERAL);
            foreach ($ephemeral as $name => &$var) {
                if (true === $var[self::ACTIVE]) {
                    $var[self::ACTIVE] = false;
                } else {
                    unset($ephemeral[$name]);
                }
            }
        } else {
            $ephemeral = array();
        }

        //~ Save in Session.
        $this->set(self::EPHEMERAL, $ephemeral);

        return $this;
    }

    /**
     * Set ephemeral variable in Session.
     *
     * @param  string $name
     * @param  mixed  $value
     * @return $this
     */
    public function setEphemeral($name, $value)
    {
        $ephemeral                        = $this->get(self::EPHEMERAL);
        $ephemeral[$name][self::ACTIVE]   = true;
        $ephemeral[$name][self::VARIABLE] = $value;
        $this->set(self::EPHEMERAL, $ephemeral);

        return $this;
    }
}
