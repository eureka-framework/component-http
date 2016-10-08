<?php

/**
 * Copyright (c) 2010-2016 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http;

/**
 * $_SESSION data wrapper class.
 *
 * @author Romain Cottard
 */
class Session extends Data
{
    /**
     * @var Data $instance Current class instance.
     */
    protected static $instance = null;

    /**
     *
     * @var string EPHEMERAL Session index name for ephemeral var in Session.
     */
    const EPHEMERAL = 'eka-ephemeral';

    /**
     * @var string ACTIVE Session index name for ephemeral var if active or not.
     */
    const ACTIVE = 'active';

    /**
     * @var string VARIABLE Session index name for ephemeral var content.
     */
    const VARIABLE = 'var';

    /**
     * Session constructor.
     */
    protected function __construct()
    {
        $this->data = $_SESSION;

        $this->clearEphemeral();
    }

    /**
     * Get Session ephemeral variable specified.
     *
     * @param  string $name
     * @return mixed  Variable value.
     */
    public function getEphemeral($name)
    {
        $ephemeral = $this->get(static::EPHEMERAL);
        if (isset($ephemeral[$name][static::VARIABLE])) {
            return $ephemeral[$name][static::VARIABLE];
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
        $ephemeral = $this->get(static::EPHEMERAL);

        return isset($ephemeral[$name]);
    }

    /**
     * Set value in session.
     *
     * @param  string $name
     * @param  mixed  $value
     * @return self
     */
    public function set($name, $value)
    {
        parent::set($name, $value);

        $_SESSION[$name] = $value;

        return $this;
    }

    /**
     * Initialize Session. Remove old ephemeral var in Session.
     *
     * @return self
     */
    public function clearEphemeral()
    {
        // Check ephemeral vars
        if ($this->has(static::EPHEMERAL)) {
            $ephemeral = $this->get(static::EPHEMERAL);
            foreach ($ephemeral as $name => &$var) {
                if (true === $var[static::ACTIVE]) {
                    $var[static::ACTIVE] = false;
                } else {
                    unset($ephemeral[$name]);
                }
            }
        } else {
            $ephemeral = array();
        }

        // Save in Session.
        $this->set(static::EPHEMERAL, $ephemeral);

        return $this;
    }

    /**
     * Set ephemeral variable in Session.
     *
     * @param  string $name
     * @param  mixed  $value
     * @return self
     */
    public function setEphemeral($name, $value)
    {
        $ephemeral                          = static::get(static::EPHEMERAL);
        $ephemeral[$name][static::ACTIVE]   = true;
        $ephemeral[$name][static::VARIABLE] = $value;
        $this->set(static::EPHEMERAL, $ephemeral);

        return $this;
    }
}