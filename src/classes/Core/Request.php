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
     * @var string
     */
    protected $method;

    /**
     * Is the server behind a trusted proxy?
     *
     * @var bool
     */
    protected static $trustProxyData;

    /**
     * Whitelist of trusted proxy server IPs.
     *
     * @var array
     */
    protected static $trustedProxies;

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
     * @param array $query
     * @param array $post
     * @param array $server
     * @param array $attributes
     * @param array $config
     */
    public function __construct(array $query = array(), array $post = array(), array $server = array(), $attributes = array(), array $config = array())
    {
        $this->query       = $query;
        $this->post        = $post;
        $this->server      = $server;
        $this->attributes  = $attributes;
        $this->config      = $config;
        $this->headers     = $this->parseHeaders($server);
        $this->method      = $this->getServer('REQUEST_METHOD', static::GET);
        static::$singleton = $this;
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
     * @return bool
     */
    public function isPost()
    {
        return ($this->method === static::POST);
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
    public function getHeader($key = null, $default = null, $delimiter = null)
    {
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
        if ($key !== null) {
            $key = $this->normalizeHeaderName($key);
        }

        return Arr::has($this->headers, $key, $delimiter);
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     *
     * @return bool
     */
    public function isMethod($method)
    {
        return ($this->method === strtoupper($method));
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
        return ($this->getHeader('X-Requested-With') === 'XMLHttpRequest');
    }


    public function getClientIp()
    {

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
     * @return bool
     */
    public static function isProxyDataTrusted()
    {
        return static::$trustProxyData;
    }

    /**
     *
     */
    public static function trustProxyData()
    {
        static::$trustProxyData = true;
    }

    /**
     *
     */
    public static function untrustProxyData()
    {
        static::$trustProxyData = false;
    }

    /**
     * @return array
     */
    public static function getTrustedProxies()
    {
        return self::$trustedProxies;
    }

    /**
     * @param array|string $trustedProxies
     */
    public static function setTrustedProxies($trustedProxies)
    {
        self::$trustedProxies = (array) $trustedProxies;
    }
}
