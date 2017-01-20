<?php

/**
 * Copyright (c) 2010-2016 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http\Message;

use Psr\Http\Message\UriInterface;

/**
 * Value object representing a URI.
 *
 * This interface is meant to represent URIs according to RFC 3986 and to
 * provide methods for most common operations. Additional functionality for
 * working with URIs can be provided on top of the interface or externally.
 * Its primary use is for HTTP requests, but may also be used in other
 * contexts.
 *
 * Instances of this interface are considered immutable; all methods that
 * might change state MUST be implemented such that they retain the internal
 * state of the current instance and return an instance that contains the
 * changed state.
 *
 * Typically the Host header will be also be present in the request message.
 * For server-side requests, the scheme will typically be discoverable in the
 * server parameters.
 *
 * @link    http://tools.ietf.org/html/rfc3986 (the URI specification)
 * @link    http://www.php-fig.org/psr/psr-7/
 *
 * @author  Romain Cottard <rco@deezer.com>
 */
class Uri implements UriInterface
{
    /**
     * @var string $charList Char list for preg match callback, used in different cases.
     */
    private static $charList = 'a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=';

    /**
     * @var string Uri scheme.
     */
    private $scheme = '';

    /**
     * @var string Uri user info.
     */
    private $userInfo = '';

    /**
     * @var string Uri host.
     */
    private $host = '';

    /**
     * @var int|null Uri port.
     */
    private $port = null;

    /**
     * @var string Uri path.
     */
    private $path = '';

    /**
     * @var string Uri query string.
     */
    private $query = '';

    /**
     * @var string Uri fragment.
     */
    private $fragment = '';

    /**
     * @param string $uri URI to parse and wrap.
     */
    public function __construct($uri = '')
    {
        if (!empty($uri)) {
            $parse = parse_url($uri);
            if ($parse === false) {
                throw new \InvalidArgumentException('Unable to parse URI: ' . $uri);
            }

            $this->setScheme($this->getStringValue($parse['scheme']));
            $this->setUserInfo($this->getStringValue($parse['user']), $this->getStringValue($parse['pass']));
            $this->setHost($this->getStringValue($parse['host']));
            $this->setPort($this->getStringValue($parse['port']));
            $this->setPath($this->getStringValue($parse['path']));
            $this->setQuery($this->getStringValue($parse['query']));
            $this->setFragment($this->getStringValue($parse['fragment']));
        }
    }

    /**
     * Retrieve the scheme component of the URI.
     *
     * If no scheme is present, this method MUST return an empty string.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.1.
     *
     * The trailing ":" character is not part of the scheme and MUST NOT be
     * added.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     * @return string The URI scheme.
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Retrieve the authority component of the URI.
     *
     * If no authority information is present, this method MUST return an empty
     * string.
     *
     * The authority syntax of the URI is:
     *
     * <pre>
     * [user-info@]host[:port]
     * </pre>
     *
     * If the port component is not set or is the standard port for the current
     * scheme, it SHOULD NOT be included.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     * @return string The URI authority, in "[user-info@]host[:port]" format.
     */
    public function getAuthority()
    {
        if (empty($this->host)) {
            return '';
        }

        $authority = $this->host;
        if (!empty($this->userInfo)) {
            $authority = $this->userInfo . '@' . $authority;
        }

        if (!empty($this->port)) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }

    /**
     * Retrieve the user information component of the URI.
     *
     * If no user information is present, this method MUST return an empty
     * string.
     *
     * If a user is present in the URI, this will return that value;
     * additionally, if the password is also present, it will be appended to the
     * user value, with a colon (":") separating the values.
     *
     * The trailing "@" character is not part of the user information and MUST
     * NOT be added.
     *
     * @return string The URI user information, in "username[:password]" format.
     */
    public function getUserInfo()
    {
        return (string) $this->userInfo;
    }

    /**
     * Retrieve the host component of the URI.
     *
     * If no host is present, this method MUST return an empty string.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.2.2.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     * @return string The URI host.
     */
    public function getHost()
    {
        return (string) $this->host;
    }

    /**
     * Retrieve the port component of the URI.
     *
     * If a port is present, and it is non-standard for the current scheme,
     * this method MUST return it as an integer. If the port is the standard port
     * used with the current scheme, this method SHOULD return null.
     *
     * If no port is present, and no scheme is present, this method MUST return
     * a null value.
     *
     * If no port is present, but a scheme is present, this method MAY return
     * the standard port for that scheme, but SHOULD return null.
     *
     * @return null|int The URI port.
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Retrieve the path component of the URI.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * Normally, the empty path "" and absolute path "/" are considered equal as
     * defined in RFC 7230 Section 2.7.3. But this method MUST NOT automatically
     * do this normalization because in contexts with a trimmed base path, e.g.
     * the front controller, this difference becomes significant. It's the task
     * of the user to handle both "" and "/".
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.3.
     *
     * As an example, if the value should include a slash ("/") not intended as
     * delimiter between path segments, that value MUST be passed in encoded
     * form (e.g., "%2F") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     * @return string The URI path.
     */
    public function getPath()
    {
        return (string) $this->path;
    }

    /**
     * Retrieve the query string of the URI.
     *
     * If no query string is present, this method MUST return an empty string.
     *
     * The leading "?" character is not part of the query and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.4.
     *
     * As an example, if a value in a key/value pair of the query string should
     * include an ampersand ("&") not intended as a delimiter between values,
     * that value MUST be passed in encoded form (e.g., "%26") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     * @return string The URI query string.
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Retrieve the fragment component of the URI.
     *
     * If no fragment is present, this method MUST return an empty string.
     *
     * The leading "#" character is not part of the fragment and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.5.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     * @return string The URI fragment.
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * Return an instance with the specified scheme.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified scheme.
     *
     * Implementations MUST support the schemes "http" and "https" case
     * insensitively, and MAY accommodate other schemes if required.
     *
     * An empty scheme is equivalent to removing the scheme.
     *
     * @param string $scheme The scheme to use with the new instance.
     * @return self A new instance with the specified scheme.
     * @throws \InvalidArgumentException for invalid or unsupported schemes.
     */
    public function withScheme($scheme)
    {
        $instance = clone $this;
        $instance->setScheme($scheme);

        return $instance;
    }

    /**
     * Return an instance with the specified user information.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified user information.
     *
     * Password is optional, but the user information MUST include the
     * user; an empty string for the user is equivalent to removing user
     * information.
     *
     * @param string      $user The user name to use for authority.
     * @param null|string $password The password associated with $user.
     * @return self A new instance with the specified user information.
     */
    public function withUserInfo($user, $password = null)
    {
        $instance = clone $this;
        $instance->setUserInfo($user, $password);

        return $instance;
    }

    /**
     * Return an instance with the specified host.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified host.
     *
     * An empty host value is equivalent to removing the host.
     *
     * @param string $host The hostname to use with the new instance.
     * @return self A new instance with the specified host.
     * @throws \InvalidArgumentException for invalid hostnames.
     */
    public function withHost($host)
    {
        $instance = clone $this;
        $instance->setHost($host);

        return $instance;
    }

    /**
     * Return an instance with the specified port.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified port.
     *
     * Implementations MUST raise an exception for ports outside the
     * established TCP and UDP port ranges.
     *
     * A null value provided for the port is equivalent to removing the port
     * information.
     *
     * @param null|int $port The port to use with the new instance; a null value
     *     removes the port information.
     * @return self A new instance with the specified port.
     * @throws \InvalidArgumentException for invalid ports.
     */
    public function withPort($port)
    {
        $instance = clone $this;
        $instance->setPath($port);

        return $instance;
    }

    /**
     * Return an instance with the specified path.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified path.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * If the path is intended to be domain-relative rather than path relative then
     * it must begin with a slash ("/"). Paths not starting with a slash ("/")
     * are assumed to be relative to some base path known to the application or
     * consumer.
     *
     * Users can provide both encoded and decoded path characters.
     * Implementations ensure the correct encoding as outlined in getPath().
     *
     * @param string $path The path to use with the new instance.
     * @return self A new instance with the specified path.
     * @throws \InvalidArgumentException for invalid paths.
     */
    public function withPath($path)
    {
        $instance = clone $this;
        $instance->setPath($path);

        return $instance;
    }

    /**
     * Return an instance with the specified query string.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified query string.
     *
     * Users can provide both encoded and decoded query characters.
     * Implementations ensure the correct encoding as outlined in getQuery().
     *
     * An empty query string value is equivalent to removing the query string.
     *
     * @param  string $query The query string to use with the new instance.
     * @return self A new instance with the specified query string.
     * @throws \InvalidArgumentException for invalid query strings.
     */
    public function withQuery($query)
    {
        if (substr($query, 0, 1) === '?') {
            $query = substr($query, 1);
        }

        $instance = clone $this;
        $instance->setQuery($query);

        return $instance;
    }

    /**
     * Return an instance with the specified URI fragment.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified URI fragment.
     *
     * Users can provide both encoded and decoded fragment characters.
     * Implementations ensure the correct encoding as outlined in getFragment().
     *
     * An empty fragment value is equivalent to removing the fragment.
     *
     * @param string $fragment The fragment to use with the new instance.
     * @return self A new instance with the specified fragment.
     */
    public function withFragment($fragment)
    {
        if (substr($fragment, 0, 1) === '#') {
            $fragment = substr($fragment, 1);
        }

        $instance = clone $this;
        $instance->setFragment($fragment);

        return $instance;
    }

    /**
     * Return the string representation as a URI reference.
     *
     * Depending on which components of the URI are present, the resulting
     * string is either a full URI or relative reference according to RFC 3986,
     * Section 4.1. The method concatenates the various components of the URI,
     * using the appropriate delimiters:
     *
     * - If a scheme is present, it MUST be suffixed by ":".
     * - If an authority is present, it MUST be prefixed by "//".
     * - The path can be concatenated without delimiters. But there are two
     *   cases where the path has to be adjusted to make the URI reference
     *   valid as PHP does not allow to throw an exception in __toString():
     *     - If the path is rootless and an authority is present, the path MUST
     *       be prefixed by "/".
     *     - If the path is starting with more than one "/" and no authority is
     *       present, the starting slashes MUST be reduced to one.
     * - If a query is present, it MUST be prefixed by "?".
     * - If a fragment is present, it MUST be prefixed by "#".
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     * @return string
     */
    public function __toString()
    {
        $uri = '';

        if (!empty($this->getScheme())) {
            $uri .= $this->getScheme() . ':';
        }

        if (!empty($this->getAuthority())) {
            $uri .= '//' . $this->getAuthority();
        }

        if (!empty($this->getPath())) {
            // Add a leading slash if necessary.
            if (!empty($uri) && substr($this->getPath(), 0, 1) !== '/') {
                $uri .= '/';
            }
            $uri .= $this->getPath();
        }

        if (!empty($this->getQuery())) {
            $uri .= '?' . $this->getQuery();
        }

        if (!empty($this->getFragment())) {
            $uri .= '#' . $this->getFragment();
        }

        return (string) $uri;
    }

    /**
     * Create new Uri instance from global $_REQUEST data.
     *
     * @return static
     */
    public static function createFromGlobal()
    {
        $instance = new self('');

        //~ Set scheme
        if (isset($_SERVER['HTTPS'])) {
            $instance->setScheme($_SERVER['HTTPS'] == 'on' ? 'https' : 'http');
        }

        //~ Set host
        if (isset($_SERVER['HTTP_HOST'])) {
            $instance->setHost($_SERVER['HTTP_HOST']);
        } elseif (isset($_SERVER['SERVER_NAME'])) {
            $instance->setHost($_SERVER['SERVER_NAME']);
        }

        //~ Set port
        if (isset($_SERVER['SERVER_PORT'])) {
            $instance->setPort($_SERVER['SERVER_PORT']);
        }

        //~ Set path
        if (isset($_SERVER['REQUEST_URI'])) {
            $instance->setPath(current(explode('?', $_SERVER['REQUEST_URI'])));
        }

        //~ Set query string
        if (isset($_SERVER['QUERY_STRING'])) {
            $instance->setQuery($_SERVER['QUERY_STRING']);
        }

        return $instance;
    }


    /**
     * Set scheme
     *
     * @param  string $scheme
     * @return self
     */
    private function setScheme($scheme)
    {
        $this->scheme = trim(strtolower($scheme), ':/');

        return $this;
    }

    /**
     * Set host
     *
     * @param  string $host
     * @return self
     */
    private function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Set port
     *
     * @param  string $port
     * @return self
     */
    private function setPort($port)
    {
        $port = (int) $port;

        if ($port < 0 || $port > 65535) {
            throw new \InvalidArgumentException('Port must be between 1 and 65535!');
        }

        if (empty($port)) {
            $port = null;
        }

        $this->port = $port;

        return $this;
    }

    /**
     * Set user info
     *
     * @param  string $user
     * @param  string $pass
     * @return self
     */
    private function setUserInfo($user, $pass)
    {
        $this->userInfo = $user . (!empty($pass) ? ':' . $pass : '');

        return $this;
    }

    /**
     * Set path
     *
     * @param  string $path
     * @return self
     */
    private function setPath($path)
    {
        $this->path =  preg_replace_callback(
            '/(?:[^' . self::$charList . ':@\/%]+|%(?![A-Fa-f0-9]{2}))/',
            [$this, 'encode'],
            $path
        );

        return $this;
    }

    /**
     * Set query
     *
     * @param  string $query
     * @return self
     */
    private function setQuery($query)
    {
        $this->query =  preg_replace_callback(
            '/(?:[^' . self::$charList . '%:@\/\?]+|%(?![A-Fa-f0-9]{2}))/',
            [$this, 'encode'],
            $query
        );

        return $this;
    }

    /**
     * Set fragment
     *
     * @param  string $fragment
     * @return self
     */
    private function setFragment($fragment)
    {
        $this->fragment = preg_replace_callback(
            '/(?:[^' . self::$charList . '%:@\/\?]+|%(?![A-Fa-f0-9]{2}))/',
            [$this, 'encode'],
            $fragment
        );

        return $this;
    }

    /**
     * Encode data
     *
     * @param  array $match
     * @return string
     */
    private function encode(array $match)
    {
        return rawurlencode($match[0]);
    }

	/**
	 * Get value as string. If not defined, get default value.
	 *
	 * @param  mixed  $var
	 * @param  string $default
	 * @return string
	 */
    private function getStringValue(&$var, $default = '')
	{
		$value = $default;

		if (isset($var)) {
			$value = (string) $var;
		}

		return $value;
	}
}
