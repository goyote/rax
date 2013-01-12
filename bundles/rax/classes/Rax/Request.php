<?php

use Rax\Helper\ArrHelper;

/**
 * @package   Rax
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 *
 * @method MatchedRoute getMatchedRoute()
 */
class Rax_Request extends Object
{
    // HTTP Methods
    const GET     = 'GET';
    const POST    = 'POST';
    const PUT     = 'PUT';
    const DELETE  = 'DELETE';
    const HEAD    = 'HEAD';
    const OPTIONS = 'OPTIONS';
    const TRACE   = 'TRACE';
    const CONNECT = 'CONNECT';

    /**
     * @var array
     */
    protected $query;

    /**
     * @var array
     */
    protected $post;

    /**
     * @var array
     */
    protected $server;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @var string
     */
    protected $method;

    /**
     * Is the server behind a trusted proxy?
     *
     * @var bool
     */
    protected $trustProxyData;

    /**
     * Whitelist of trusted proxy server IPs.
     *
     * @var array
     */
    protected $trustedProxies;

    /**
     * @var string
     */
    protected $uri;

    /**
     * @var MatchedRoute
     */
    protected $matchedRoute;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $action;

    /**
     * Singleton instance.
     *
     * @var Request
     */
    protected static $singleton;

    /**
     * @param array  $query
     * @param array  $post
     * @param array  $server
     * @param array  $attributes
     * @param ArrObj $config
     */
    public function __construct(array $query = array(), array $post = array(), array $server = array(), $attributes = array(), ArrObj $config)
    {
        $this->query      = $query;
        $this->post       = $post;
        $this->server     = $server;
        $this->attributes = $attributes;
        $this->config     = $config;
    }

    /**
     * Gets a singleton instance.
     *
     * @return Request
     */
    public static function getSingleton()
    {
        return static::$singleton;
    }

    /**
     * @param Request $singleton
     */
    public static function setSingleton(Request $singleton)
    {
        static::$singleton = $singleton;
    }

    /**
     * @param array|string $key
     * @param mixed        $default
     * @param string       $delimiter
     *
     * @return array|mixed
     */
    public function getQuery($key = null, $default = null, $delimiter = null)
    {
        return ArrHelper::get($this->query, $key, $default, $delimiter);
    }

    /**
     * @param array|string $key
     * @param string       $delimiter
     *
     * @return bool
     */
    public function hasQuery($key, $delimiter = null)
    {
        return ArrHelper::has($this->query, $key, $delimiter);
    }

    /**
     * @param array|string $key
     * @param mixed        $default
     * @param string       $delimiter
     *
     * @return array|mixed
     */
    public function getPost($key = null, $default = null, $delimiter = null)
    {
        return ArrHelper::get($this->post, $key, $default, $delimiter);
    }

    /**
     * @param array|string $key
     * @param string       $delimiter
     *
     * @return bool
     */
    public function hasPost($key, $delimiter = null)
    {
        return ArrHelper::has($this->post, $key, $delimiter);
    }

    /**
     * @param array|string $key
     * @param mixed        $default
     * @param string       $delimiter
     *
     * @return array|mixed
     */
    public function getServer($key = null, $default = null, $delimiter = null)
    {
        return ArrHelper::get($this->server, $key, $default, $delimiter);
    }

    /**
     * @param array|string $key
     * @param string       $delimiter
     *
     * @return bool
     */
    public function hasServer($key, $delimiter = null)
    {
        return ArrHelper::has($this->server, $key, $delimiter);
    }

    /**
     * @param array|string $key
     * @param mixed        $value
     * @param string       $delimiter
     *
     * @return Request
     */
    public function setAttribute($key, $value = null, $delimiter = null)
    {
        ArrHelper::set($this->attributes, $key, $value, $delimiter);

        return $this;
    }

    /**
     * @param array|string $key
     * @param mixed        $default
     * @param string       $delimiter
     *
     * @return array|mixed
     */
    public function getAttribute($key = null, $default = null, $delimiter = null)
    {
        return ArrHelper::get($this->attributes, $key, $default, $delimiter);
    }

    /**
     * @param array|string $key
     * @param string       $delimiter
     *
     * @return bool
     */
    public function hasAttribute($key, $delimiter = null)
    {
        return ArrHelper::has($this->attributes, $key, $delimiter);
    }

    /**
     * @param array|string $key
     * @param mixed        $default
     * @param string       $delimiter
     *
     * @return array|mixed
     */
    public function getPostQuery($key = null, $default = null, $delimiter = null)
    {
        return $this->getPost($key, $this->getQuery($key, $default, $delimiter), $delimiter);
    }

    /**
     * @param array|string $key
     * @param mixed        $default
     * @param string       $delimiter
     *
     * @return array|mixed
     */
    public function getHeader($key = null, $default = null, $delimiter = null)
    {
        if (null === $this->headers) {
            $this->headers = $this->parseHeaders($this->server);
        }

        if (null !== $key) {
            $key = $this->normalizeHeaderName($key);
        }

        return ArrHelper::get($this->headers, $key, $default, $delimiter);
    }

    /**
     * @param array|string $key
     * @param string       $delimiter
     *
     * @return bool
     */
    public function hasHeader($key, $delimiter = null)
    {
        if ($this->headers === null) {
            $this->headers = $this->parseHeaders($this->server);
        }

        if (null !== $key) {
            $key = $this->normalizeHeaderName($key);
        }

        return ArrHelper::has($this->headers, $key, $delimiter);
    }

    /**
     * @param array $server
     *
     * @return array
     */
    public function parseHeaders(array $server)
    {
        $headers = array();
        foreach ($server as $key => $value) {
            if (0 === strpos($key, 'HTTP_')) {
                $headers[$this->normalizeHeaderName(substr($key, 5))] = $value;
            } elseif (in_array($key, array('CONTENT_LENGTH', 'CONTENT_MD5', 'CONTENT_TYPE'))) {
                $headers[$this->normalizeHeaderName($key)] = $value;
            }
        }

        return $headers;
    }

    /**
     * @param string $header
     *
     * @return string
     */
    public function normalizeHeaderName($header)
    {
        return str_replace('_', '-', strtolower($header));
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = strtoupper($method);
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        if (null === $this->method) {
            $this->setMethod($this->getServer('REQUEST_METHOD', static::GET));
        }

        return $this->method;
    }

    /**
     * @param string $method
     *
     * @return bool
     */
    public function isMethod($method)
    {
        return (strtoupper($method) === $this->getMethod());
    }

    /**
     * @return bool
     */
    public function isPost()
    {
        return (static::POST === $this->getMethod());
    }

    /**
     * Checks if the request is Ajax.
     *
     *     // Within a controller
     *     if ($this->getRequest()->isAjax())
     *
     *     // Outside a controller
     *     if (Request::getSingleton()->isAjax())
     *
     *     // Within a template
     *     {{ app.request.isAjax() }}
     *
     * @return bool
     */
    public function isAjax()
    {
        return ('XMLHttpRequest' === $this->getServer('HTTP_X_REQUESTED_WITH'));
    }

    /**
     * Returns the client's IP address.
     *
     * If the server is behind a reverse proxy, you will need to set the
     * "trustProxyData" config value to "true" and add the reverse proxy's IP
     * to the list of "trustedProxies". Lastly, make sure you forward the client
     * IP through "X-Forwarded-For" or "Client-IP".
     *
     * @throws RuntimeException
     *
     * @return string
     */
    public function getClientIp()
    {
        if ($this->isProxyDataTrusted() && in_array($this->getServer('REMOTE_ADDR'), $this->getTrustedProxies())) {
            if (!$clientIp = $this->getServer('HTTP_X_FORWARDED_FOR', $this->getServer('HTTP_CLIENT_IP'))) {
                throw new RuntimeException('The client IP was not forwarded by the reverse proxy');
            }

            return trim(current(explode(',', $clientIp)));
        }

        return $this->getServer('REMOTE_ADDR');
    }

    /**
     * Returns the server ip.
     *
     *     // Within a controller
     *     $this->getRequest()->getServerIp();
     *
     *     // Outside a controller
     *     Request::getSingleton()->getServerIp();
     *
     *     // Within a template
     *     {{ app.request.getServerIp() }}
     *
     * @return string
     */
    public function getServerIp()
    {
        return $this->getServer('SERVER_ADDR');
    }

    /**
     * @param bool $trustProxyData
     *
     * @return Request
     */
    public function trustProxyData($trustProxyData = true)
    {
        $this->trustProxyData = (bool) $trustProxyData;

        return $this;
    }

    /**
     * @return bool
     */
    public function isProxyDataTrusted()
    {
        if (null === $this->trustProxyData) {
            $this->trustProxyData($this->config->get('trustProxyData', false));
        }

        return $this->trustProxyData;
    }

    /**
     * @param array|string $trustedProxies
     *
     * @return Request
     */
    public function setTrustedProxies($trustedProxies)
    {
        $this->trustedProxies = (array) $trustedProxies;

        return $this;
    }

    /**
     * @return array
     */
    public function getTrustedProxies()
    {
        if (null === $this->trustedProxies) {
            $this->setTrustedProxies($this->config->get('trustedProxies', array('127.0.0.1')));
        }

        return $this->trustedProxies;
    }

    /**
     * @return bool
     */
    public function isSecure()
    {
        return (
            filter_var($this->getServer('HTTPS'), FILTER_VALIDATE_BOOLEAN) ||
            (
                $this->trustProxyData() && (
                filter_var($this->getServer('HTTP_SSL_HTTPS'), FILTER_VALIDATE_BOOLEAN) ||
                filter_var($this->getServer('HTTP_X_FORWARDED_PROTO'), FILTER_VALIDATE_BOOLEAN)
            ))
        );
    }

    /**
     * @return string
     */
    public function getUri()
    {
        if (null === $this->uri) {
            $this->uri = $this->detectUri();
        }

        return $this->uri;
    }

    /**
     * @return string
     */
    protected function detectUri()
    {
        return rawurldecode(parse_url($this->getServer('REQUEST_URI'), PHP_URL_PATH));
    }

    public function setMatchedRoute(MatchedRoute $matchedRoute)
    {
        $this->matchedRoute = $matchedRoute;
    }

    public function getController()
    {
        return $this->matchedRoute->getController();
    }

    public function getAction()
    {
        return $this->matchedRoute->getAction();
    }
}
