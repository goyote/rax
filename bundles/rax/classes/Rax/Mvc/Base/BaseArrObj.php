<?php

namespace Rax\Mvc\Base;

use ArrayObject;
use Rax\Helper\ArrHelper;
use Rax\Mvc\ArrObj;

/**
 * ArrObj class.
 */
class BaseArrObj extends ArrayObject
{
    /**
     * @param array|object $input
     * @param int          $flags
     */
    public function __construct($input = null, $flags = ArrayObject::ARRAY_AS_PROPS)
    {
        parent::__construct($input, $flags);
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @param string $delimiter
     *
     * @return self
     */
    public function set($key, $value, $delimiter = null)
    {
        ArrHelper::set($this, $key, $value, $delimiter);

        return $this;
    }

    /**
     * Gets a value from the configuration.
     *
     * @param string $key
     * @param mixed  $default
     * @param string $delimiter
     *
     * @return ArrObj|mixed
     */
    public function get($key = null, $default = null, $delimiter = null)
    {
        return ArrHelper::get($this, $key, $default, $delimiter);
    }

    /**
     * @param string $key
     * @param string $delimiter
     *
     * @return bool
     */
    public function has($key, $delimiter = null)
    {
        return ArrHelper::has($this, $key, $delimiter);
    }

    /**
     * @param string $key
     * @param string $delimiter
     *
     * @return self
     */
    public function delete($key, $delimiter = null)
    {
        ArrHelper::delete($this, $key, $delimiter);

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return self
     */
    public function unshift($key, $value)
    {
        $this->exchangeArray(array($key => $value) + $this->getArrayCopy());

        return $this;
    }

    /**
     * Casts object into an array and returns it.
     *
     * Alias for `ArrayObject::getArrayCopy()`.
     *
     * @return array
     */
    public function asArray()
    {
        return $this->getArrayCopy();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return serialize($this->getArrayCopy());
    }
}
