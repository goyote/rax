<?php

namespace Rax\Routing\Base;

use ArrayAccess;
use Rax\Data\Config;
use Rax\Helper\Arr;
use Rax\Http\Response;
use Rax\Mvc\MatchedRoute;
use Rax\Routing\Route;
use Rax\Http\Request;
use Rax\Mvc\Service;

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
     * @var Service
     */
    protected $service;

    /**
     * @param Config  $config
     * @param Service $service
     */
    public function __construct(Config $config, Service $service)
    {
        $this->routes  = $this->parse($config->get('routes'));
        $this->service = $service;
    }

    /**
     * @param array|ArrayAccess $config
     *
     * @return Route[]
     */
    protected function parse($config)
    {
        $routes = array();
        foreach ($config as $name => $route) {
            $routes[$name] = new Route(
                $name,
                $route['path'],
                $route['controller'],
                Arr::get($route, 'defaults', array()),
                Arr::get($route, 'rules', array()),
                Arr::get($route, 'filters', array())
            );
        }

        return $routes;
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
     * @param Request $request
     *
     * @return MatchedRoute
     */
    public function match(Request $request)
    {
        foreach ($this->routes as $route) {
            /** @noinspection PhpAssignmentInConditionInspection */
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
     * @return bool|MatchedRoute
     */
    public function matchRoute(Request $request, Route $route)
    {
        if (!$this->filter($route)) {
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

        return new MatchedRoute($route, $params);
    }

    /**
     * @param Route $route
     *
     * @return bool|Response
     */
    public function filter(Route $route)
    {
        foreach ($route->getFilters() as $name => $value) {
            $filter = $this->service->build('Rax\\Routing\\Filter\\'.ucfirst($name).'RouteFilter');

            if (!$this->service->call($filter, 'filter', array('value' => $value))) {
                return false;
            }
        }

        return true;
    }
}
