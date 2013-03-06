<?php

namespace Rax\Data\Base;

use Rax\Data\ArrObj;
use Rax\Data\DataBank;
use Rax\Data\Driver\DriverInterface;
use Rax\Helper\Arr;

/**
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseDataBank
{
    /**
     * @var DriverInterface[]
     */
    protected $drivers = array();

    /**
     * @var array
     */
    protected $loadedData = array();

    /**
     * @var array
     */
    protected $queuedData = array();

    /**
     * Returns a new chainable instance.
     *
     * @return DataBank
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Sets the data drivers.
     *
     * @param DriverInterface[] $drivers
     *
     * @return DataBank
     */
    public function setDrivers($drivers)
    {
        $this->drivers = $drivers;

        return $this;
    }

    /**
     * Returns the data drivers.
     *
     * @return DriverInterface[]
     */
    public function getDrivers()
    {
        return $this->drivers;
    }

    /**
     * Adds a data driver.
     *
     * @param DriverInterface $driver
     *
     * @return DataBank
     */
    public function addDriver(DriverInterface $driver)
    {
        $this->drivers[] = $driver;

        return $this;
    }

    /**
     * Removes a data driver.
     *
     * @param DriverInterface $driver
     *
     * @return DataBank
     */
    public function removeDriver(DriverInterface $driver)
    {
        if (false !== ($key = array_search($driver, $this->drivers))) {
            unset($this->drivers[$key]);
        }

        return $this;
    }

    /**
     * Sets the queued data.
     *
     * @param array $queuedData
     *
     * @return DataBank
     */
    public function setQueuedData(array $queuedData)
    {
        $this->queuedData = $queuedData;

        return $this;
    }

    /**
     * Returns the queued data.
     *
     * @return array
     */
    public function getQueuedData()
    {
        return $this->queuedData;
    }

    /**
     * Queues data for storage.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return DataBank
     */
    public function set($key, $value = null)
    {
        Arr::set($this->queuedData, $key, $value);

        return $this;
    }

    /**
     * Returns a data value.
     *
     * @param string $key
     * @param mixed  $default
     * @param bool   $reload
     *
     * @return mixed
     */
    public function get($key, $default = null, $reload = false)
    {
        $name = current(explode('.', $key));

        if ($reload || !isset($this->loadedData[$name])) {
            $this->load($name);
        }

        return Arr::get($this->loadedData, $key, $default);
    }

    /**
     * Loads the data.
     *
     * @param string $key
     *
     * @return ArrObj
     */
    public function load($key)
    {
        $data = array();
        foreach ($this->drivers as $driver) {
            $data = Arr::merge($data, $driver->load($key));
        }

        return ($this->loadedData[$key] = new ArrObj($data));
    }

    /**
     * Save the queued data.
     *
     * @return DataBank
     */
    public function save()
    {
        foreach ($this->drivers as $driver) {
            $driver->save($this->queuedData);
        }

        return $this;
    }
}
