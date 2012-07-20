<?php

/**
 *
 */
class Core_Config_Group extends ArrayObject
{
    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return Core_Config_Group
     */
    public function set($key, $value)
    {
        $this->offsetSet($key, $value);

        return $this;
    }

    /**
     * Gets a value from the configuration.
     *
     * @param string $key
     * @param mixed  $default
     * @param string $delimiter
     *
     * @return mixed|null
     */
    public function get($key, $default = null, $delimiter = null)
    {
        if ($this->offsetExists($key)) {
            return $this->offsetGet($key);
        }

        return Arr::get($this, $key, $default, $delimiter);
    }

    /**
     * @param string $key
     * @param string $delimiter
     *
     * @return bool
     */
    public function has($key, $delimiter = null)
    {
        if ($this->offsetExists($key)) {
            return true;
        }

        return Arr::has($this, $key, $delimiter);
    }

    /**
     * @param string $key
     *
     * @return Core_Config_Group
     */
    public function delete($key)
    {
        if ($this->offsetExists($key)) {
            $this->offsetUnset($key);
        }

        return $this;
    }

    /**
     * Casts the object into an array and returns it.
     *
     * Alias for `ArrayObject::getArrayCopy()`.
     *
     * @return array
     */
    public function asArray()
    {
        return $this->getArrayCopy();
    }
}
