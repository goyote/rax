<?php

namespace Rax\Mvc\Base;

use Rax\Mvc\RouteMatch;
use Rax\Mvc\Route;
use Rax\Mvc\Router;
use Rax\Http\Request;
use Rax\Mvc\Validator\RouteValidator;

/**
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseRouter
{
    /**
     * @var Route[]
     */
    protected $routes;

    /**
     * @var RouteValidator
     */
    public $routeValidator;

    /**
     * @param Route[]        $routes
     * @param RouteValidator $routeValidator
     */
    public function __construct(array $routes = array(), RouteValidator $routeValidator = null)
    {
        $this->routes         = $routes;
        $this->routeValidator = $routeValidator;
    }

    /**
     * Sets the routes.
     *
     * @param Route[] $routes
     *
     * @return Router
     */
    public function setRoutes(array $routes)
    {
        $this->routes = $routes;

        return $this;
    }

    /**
     * Returns the routes.
     *
     * @return Route[]
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Sets the route validator.
     *
     * @param RouteValidator $routeValidator
     *
     * @return Router
     */
    public function setRouteValidator(RouteValidator $routeValidator)
    {
        $this->routeValidator = $routeValidator;

        return $this;
    }

    /**
     * Returns the route validator.
     *
     * @return RouteValidator
     */
    public function getRouteValidator()
    {
        return $this->routeValidator;
    }

    /**
     * @param Request $request
     *
     * @return RouteMatch
     */
    public function match(Request $request)
    {
        $this->routeValidator->setRequest($request);

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
     * @return RouteMatch
     */
    public function matchRoute(Request $request, Route $route)
    {
        if (!$this->routeValidator->isValid($route)) {
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

        return new RouteMatch($route, $params);
    }
}
