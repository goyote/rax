<?php

namespace Rax\Mvc\Validator\Base;

use Rax\Http\Request;
use Rax\Mvc\ServerMode;
use Rax\Mvc\Route;
use Rax\Mvc\Validator\RouteValidator;

/**
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseRouteValidator
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * Sets the request.
     *
     * @param Request $request
     *
     * @return RouteValidator
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Returns the request.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Checks if the route is valid.
     *
     * @param Route $route
     *
     * @return bool
     */
    public function isValid($route)
    {
        foreach ($route->getSpecialRules() as $rule => $value) {
            $method = 'is'.ucfirst($rule).'Valid';
            if (!$this->$method($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if "ajax" is valid.
     *
     * @param bool $value
     *
     * @return bool
     */
    public function isAjaxValid($value)
    {
        return ($value === $this->request->isAjax());
    }

    /**
     * Checks if "secure" is valid.
     *
     * @param bool $value
     *
     * @return bool
     */
    public function isSecureValid($value)
    {
        return ($value === $this->request->isSecure());
    }

    /**
     * Checks if "method" is valid.
     *
     * @param string $value
     *
     * @return bool
     */
    public function isMethodValid($value)
    {
        return $this->request->isMethod(explode('|', $value));
    }

    /**
     * Checks if "clientIp" is valid.
     *
     * @param string $value
     *
     * @return bool
     */
    public function isClientIpValid($value)
    {
        return preg_match('#^'.$value.'$#', $this->request->getClientIp());
    }

    /**
     * Checks if "serverIp" is valid.
     *
     * @param string $value
     *
     * @return bool
     */
    public function isServerIpValid($value)
    {
        return preg_match('#^'.$value.'$#', $this->request->getServerIp());
    }

    /**
     * Checks if "environment" is valid.
     *
     * @param string $value
     *
     * @return bool
     */
    public function isEnvironmentValid($value)
    {
        return ServerMode::is(explode('|', $value));
    }
}
