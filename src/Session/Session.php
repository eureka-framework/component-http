<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http\Session;

/**
 * $_SESSION data wrapper class.
 *
 * @author Romain Cottard
 */
class Session
{
    /** @var string EPHEMERAL Session index name for ephemeral var in Session. */
    private const EPHEMERAL = '_ephemeral';

    /** @var string ACTIVE Session index name for ephemeral var if active or not. */
    private const ACTIVE = 'active';

    /** @var string VARIABLE Session index name for ephemeral var content. */
    private const VARIABLE = 'var';

    /** @var Session $instance Current class instance. */
    protected static $instance = null;

    /** @var array $session */
    protected $session = [];

    /**
     * Session constructor.
     */
    private function __construct()
    {
        $this->session = &$_SESSION;

        $this->clearEphemeral();
    }

    /**
     * Singleton pattern method.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (null === static::$instance) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * If session have given key.
     *
     * @param  string
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->session);
    }

    /**
     * Get session value.
     *
     * @param string|int $key
     * @param $default
     * @return mixed|null
     */
    public function get(string $key, $default = null)
    {
        if (!$this->has($key)) {
            return $default;
        }

        return $this->session[$key];
    }

    /**
     * Set value for a given key.
     *
     * @param  string $key
     * @param  mixed $value
     * @return self
     */
    public function set(string $key, $value): self
    {
        $this->session[$key] = $value;

        return $this;
    }

    /**
     * Remove key from bag container.
     * If key not exists, must throw an BagKeyNotFoundException
     *
     * @param  string $key
     * @return static
     */
    public function remove(string $key): self
    {
        if ($this->has($key)) {
            unset($this->session[$key]);
        }

        return $this;
    }

    /**
     * Get Session ephemeral variable specified.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed  Variable value.
     */
    public function getEphemeral(string $name, $default = null)
    {
        $ephemeral = $this->get(self::EPHEMERAL);
        if (!isset($ephemeral[$name][self::VARIABLE]) && !array_key_exists($name, $ephemeral)) {
            return $default;
        }

        return $ephemeral[$name][self::VARIABLE];
    }

    /**
     * Check if have specified ephemeral var in Session.
     *
     * @param  string $name Index Session name.
     * @return bool
     */
    public function hasEphemeral(string $name): bool
    {
        $ephemeral = $this->get(self::EPHEMERAL);

        return array_key_exists($name, $ephemeral);
    }

    /**
     * Initialize Session. Remove old ephemeral var in Session.
     *
     * @return $this
     */
    public function clearEphemeral(): self
    {
        $ephemeral = [];

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
        }

        //~ Save in Session.
        $this->set(self::EPHEMERAL, $ephemeral);

        return $this;
    }

    /**
     * Set ephemeral variable in Session.
     *
     * @param  string $name
     * @param  mixed $value
     * @return $this
     */
    public function setEphemeral(string $name, $value): self
    {
        $ephemeral                        = $this->get(self::EPHEMERAL);
        $ephemeral[$name][self::ACTIVE]   = true;
        $ephemeral[$name][self::VARIABLE] = $value;
        $this->set(self::EPHEMERAL, $ephemeral);

        return $this;
    }
}
