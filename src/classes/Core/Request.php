<?php

/**
 *
 */
class Core_Request
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
     * Singleton instance.
     *
     * @var self
     */
    protected static $singleton;

    /**
     * Gets a singleton instance.
     *
     * @return self
     */
    public static function getSingleton()
    {
        return static::$singleton;
    }

    /**
     * @param array  $query
     * @param array  $post
     * @param array  $server
     * @param array  $attributes
     * @param ArrObj $config
     */
    public function __construct(array $query = array(), array $post = array(), array $server = array(), $attributes = array(), ArrObj $config)
    {
        $this->query       = $query;
        $this->post        = $post;
        $this->server      = $server;
        $this->attributes  = $attributes;
        $this->config      = $config;
        static::$singleton = $this;
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
        return Arr::get($this->query, $key, $default, $delimiter);
    }

    /**
     * @param array|string $key
     * @param string       $delimiter
     *
     * @return bool
     */
    public function hasQuery($key, $delimiter = null)
    {
        return Arr::has($this->query, $key, $delimiter);
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
        return Arr::get($this->post, $key, $default, $delimiter);
    }

    /**
     * @param array|string $key
     * @param string       $delimiter
     *
     * @return bool
     */
    public function hasPost($key, $delimiter = null)
    {
        return Arr::has($this->post, $key, $delimiter);
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
        return Arr::get($this->server, $key, $default, $delimiter);
    }

    /**
     * @param array|string $key
     * @param string       $delimiter
     *
     * @return bool
     */
    public function hasServer($key, $delimiter = null)
    {
        return Arr::has($this->server, $key, $delimiter);
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
        return Arr::get($this->attributes, $key, $default, $delimiter);
    }

    /**
     * @param array|string $key
     * @param string       $delimiter
     *
     * @return bool
     */
    public function hasAttribute($key, $delimiter = null)
    {
        return Arr::has($this->attributes, $key, $delimiter);
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
        if ($this->headers === null) {
            $this->headers = $this->parseHeaders($this->server);
        }

        if ($key !== null) {
            $key = $this->normalizeHeaderName($key);
        }

        return Arr::get($this->headers, $key, $default, $delimiter);
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

        if ($key !== null) {
            $key = $this->normalizeHeaderName($key);
        }

        return Arr::has($this->headers, $key, $delimiter);
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
            if (strpos($key, 'HTTP_') === 0) {
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
        if ($this->method === null) {
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
        return ($this->getMethod() === strtoupper($method));
    }

    /**
     * @return bool
     */
    public function isPost()
    {
        return ($this->getMethod() === static::POST);
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
        return ($this->getServer('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest');
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
     * @return self
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
        if ($this->trustProxyData === null) {
            $this->trustProxyData($this->config->get('trustProxyData', false));
        }

        return $this->trustProxyData;
    }

    /**
     * @param array|string $trustedProxies
     *
     * @return self
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
        if ($this->trustedProxies === null) {
            $this->setTrustedProxies($this->config->get('trustedProxies', array('127.0.0.1')));
        }

        return $this->trustedProxies;
    }
}
