<?php

namespace Rax\Data\Driver;

/**
 * Data driver interface.
 *
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
interface DriverInterface
{
    /**
     * Loads the data from source.
     *
     * @param string $key
     *
     * @return array
     */
    public function load($key);

    /**
     * Save data to source.
     *
     * @param array $queuedData
     */
    public function save(array $queuedData);
}
