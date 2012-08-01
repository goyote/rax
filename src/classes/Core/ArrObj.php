<?php

/**
 *
 */
class Core_ArrObj extends ArrayObject
{
    /**
     * @param string $key
     * @param mixed  $value
     * @param string $delimiter
     *
     * @return self
     */
    public function set($key, $value, $delimiter = Text::PATH_DELIMITER)
    {
        Arr::set($this, $key, $value, $delimiter);

        return $this;
    }

    /**
     * Gets a value from the configuration.
     *
     * @param string $key
     * @param mixed  $default
     * @param string $delimiter
     *
     * @return mixed
     */
    public function get($key, $default = null, $delimiter = null)
    {
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
        return Arr::has($this, $key, $delimiter);
    }

    /**
     * @param string $key
     * @param string $delimiter
     *
     * @return self
     */
    public function delete($key, $delimiter = Text::PATH_DELIMITER)
    {
        Arr::delete($this, $key, $delimiter);

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return array
     */
    public function unshift($key, $value)
    {
        return $this->exchangeArray(array($key => $value) + $this->getArrayCopy());
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
