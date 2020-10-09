<?php declare(strict_types=1);

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http;

/**
 * Class QueryParameter
 *
 * @author Romain Cottard
 */
class QueryParameters
{
    /** @var array parameters */
    private $parameters = [];

    /**
     * QueryParameter constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param  string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->parameters[$name]);
    }

    /**
     * @param  string $name
     * @param  mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        if ($this->has($name)) {
            return $this->parameters[$name];
        }

        return $default;
    }
}
