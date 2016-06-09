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
 * @version 2.1.0
 */
class Session extends Data
{
    /**
     * Current class instance.
     *
     * @var Data $instance
     */
    protected static $instance = null;

    /**
     * Session index name for ephemeral var in Session.
     * @var    string  EPHEMERAL
     */
    const EPHEMERAL = 'eka-ephemeral';

    /**
     * Session index name for ephemeral var if active or not.
     * @var    string  ACTIVE
     */
    const ACTIVE = 'active';

    /**
     * Session index name for ephemeral var content.
     * @var    string  VARIABLE
     */
    const VARIABLE = 'var';

    /**
     * Session constructor.
     *
     * @return Session Current instance
     */
    protected function __construct()
    {
        $this->data = $_SESSION;

        $this->clearEphemeral();
    }

    /**
     * Get Session ephemeral variable specified.
     *
     * @param    string $name
     * @return   mixed  Variable value.
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
     * @param    string $name Index Session name.
     * @return   boolean
     */
    public function hasEphemeral($name)
    {
        $ephemeral = $this->get(static::EPHEMERAL);

        return isset($ephemeral[$name]);
    }

    /**
     * Set value in session.
     *
     * @param string $name
     * @param mixed  $value
     * @return $this
     */
    public function set($name, $value) {
        parent::set($name, $value);

        $_SESSION[$name] = $value;

        return $this;
    }

    /**
     * Initialize Session. Remove old ephemerals var in Session.
     *
     * @return   $this
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
     * @param    string $name
     * @param    mixed  $value
     * @return   $this
     */
    public function setEphemeral($name, $value)
    {
        $ephemeral                           = static::get(static::EPHEMERAL);
        $ephemeral[$name][static::ACTIVE]   = true;
        $ephemeral[$name][static::VARIABLE] = $value;
        $this->set(static::EPHEMERAL, $ephemeral);

        return $this;
    }

}