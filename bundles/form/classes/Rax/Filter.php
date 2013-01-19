<?php

/**
 * @package   Rax\Form
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
abstract class Rax_Filter
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor. proxy to set options
     */
    public function __construct()
    {
        $this->setOptions(func_get_args());
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    abstract public function filter($value);

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @throws Error
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getOption($key)
    {
        if (!isset($this->options[$key])) {
            throw new Error('%s::getOption() "%s" option does not exist', array(PhpHelper::getType($this), $key));
        }

        return $this->options[$key];
    }
}
