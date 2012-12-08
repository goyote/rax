<?php

/**
 * @package   Rax
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 *
 * @method Router setRoutes(array $routes) Sets the routes.
 * @method array  getRoutes()              Returns the loaded routes.
 */
class Rax_Router extends Object
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
     * @param Request $request
     *
     * @return array
     */
    public function match(Request $request)
    {
        foreach ($this->routes as $route) {
            if ($match = $this->matchRoute($request, $route)) {
                return $match;
            }
        }

        return false;
    }

    /**
     * @param Request $request
     * @param Route   $route
     *
     * @return bool
     */
    public function matchRoute(Request $request, Route $route)
    {
        if (!$this->isSpecialRulesValid($request, $route)) {
            return false;
        }

        if (!preg_match($route->getRegex(), $request->getUri(), $matches)) {
            return false;
        }

        array_shift($matches);

        $params = $route->getDefaults();
        foreach ($matches as $key => $value) {
            if (!is_int($key)) {
                $params[$key] = $value;
            }
        }

        return $params;
    }

    /**
     * @param Request $request
     * @param Route   $route
     *
     * @return bool
     */
    public function isSpecialRulesValid(Request $request, Route $route)
    {
        if ($route->hasRule('ajax') && $route->getRule('ajax') !== $request->isAjax()) {
            return false;
        }

        if ($route->hasRule('secure') && $route->getRule('secure') !== $request->isSecure()) {
            return false;
        }

        if ($route->hasRule('method') && !preg_match('#^'.$route->getRule('method').'$#i', $request->getMethod())) {
            return false;
        }

        if ($route->hasRule('clientIp') && !preg_match('#^'.$route->getRule('clientIp').'$#', $request->getClientIp())) {
            return false;
        }

        if ($route->hasRule('serverIp') && !preg_match('#^'.$route->getRule('serverIp').'$#', $request->getServerIp())) {
            return false;
        }

        if ($route->hasRule('environment') && !preg_match('#^'.$route->getRule('environment').'$#i', Environment::get(true))) {
            return false;
        }

        return true;
    }
}
