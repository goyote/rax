<?php

/**
 * @package   Rax
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class Rax_Object
{
    /**
     * @throws Barf
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
            throw new Barf('Call to undefined method %s::%s()', array(Php::getType($this), $method));
        }

        $property    = substr($method, 3);
        $property[0] = strtolower($property[0]);

        if ('set' === $function) {
            if (!array_key_exists(0, $arguments)) {
                throw new Barf('Missing argument 1 for %s::%s()', array(Php::getType($this), $method));
            }
            $this->$property = $arguments[0];
        }

        if ('get' === $function) {
            if (!property_exists($this, $property)) {
                if (!property_exists($this, $property.'s')) {
                    throw new Barf('Undefined property: %s::%s', array(Php::getType($this), $property));
                }
                if (!array_key_exists(0, $arguments)) {
                    throw new Barf('Missing argument 1 for %s::%s()', array(Php::getType($this), $method));
                }
                $property .= 's';
                $default = isset($arguments[1]) ? $arguments[1] : null;

                return array_key_exists($arguments[0], $this->$property) ? $this->$property[$arguments[0]] : $default;
            }

            return $this->$property;
        }

        if ('has' === $function) {
            $property .= 's';
            if (!property_exists($this, $property)) {
                throw new Barf('Undefined property: %s::%s', array(Php::getType($this), $property));
            }
            if (!array_key_exists(0, $arguments)) {
                throw new Barf('Missing argument 1 for %s::%s()', array(Php::getType($this), $method));
            }

            return array_key_exists($arguments[0], $this->$property);
        }

        return $this;
    }
}
