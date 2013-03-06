<?php

namespace Rax\Mvc\Base;

use Rax\Helper\Php;
use Rax\Mvc\Exception;

/**
 * @package   Rax
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseObject
{
    /**
     * @throws Exception
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $function = substr($method, 0, 3);
        if (!in_array($function, array('set', 'get', 'has'))) {
            throw new Exception('Call to undefined method %s::%s()', array(Php::getType($this), $method));
        }

        $property = lcfirst(substr($method, 3));

        if ('set' === $function) {
            if (!array_key_exists(0, $arguments)) {
                throw new Exception('Missing argument 1 for %s::%s()', array(Php::getType($this), $method));
            }
            $this->$property = $arguments[0];
        } elseif ('get' === $function) {
            if (!property_exists($this, $property)) {
                $property .= 's';
                if (!property_exists($this, $property)) {
                    throw new Exception('Undefined property: %s::%s', array(Php::getType($this), $method));
                }
                if (!array_key_exists(0, $arguments)) {
                    throw new Exception('Missing argument 1 for %s::%s()', array(Php::getType($this), $method));
                }
                $default = isset($arguments[1]) ? $arguments[1] : null;

                return array_key_exists($arguments[0], $this->$property) ? $this->{$property}[$arguments[0]] : $default;
            }

            return $this->$property;
        } elseif ('has' === $function) {
            $property .= 's';
            if (!property_exists($this, $property)) {
                throw new Exception('Undefined property: %s::%s', array(Php::getType($this), $method));
            }
            if (!array_key_exists(0, $arguments)) {
                throw new Exception('Missing argument 1 for %s::%s()', array(Php::getType($this), $method));
            }

            return array_key_exists($arguments[0], $this->$property);
        }

        return $this;
    }
}
