<?php

/**
 * @package   Rax
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class Rax_Router
{
    /**
     * @var array
     */
    protected $routes;

    /**
     * @param array $routes
     */
    public function __construct(array $routes = array())
    {
        $this->routes = $routes;
    }

    /**
     * @param array $routes
     *
     * @return Router
     */
    public function setRoutes(array $routes)
    {
        $this->routes = $routes;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param string $uri
     *
     * @return array
     */
    public function match($uri)
    {
        foreach ($this->routes as $route) {
            /** @noinspection PhpAssignmentInConditionInspection */
            if ($match = $this->_match($uri, $route)) {
                return $match;
            }
        }

        return false;
    }

    /**
     * @param string $uri
     * @param Route  $route
     *
     * @return bool
     */
    public function _match($uri, Route $route)
    {
        echo '<pre>';
        $regex = $route->getRegex();

        if (!preg_match($regex[2], $uri, $matches)) {
            return false;
        }

        array_shift($matches);

        $params = $route->getDefaults();
        foreach ($matches as $key => $value) {
            if (!is_int($key)) {
                $params[$key] = $value;
            }
        }

        Debug::dump($params);
        return false;
    }
}
