<?php

/**
 * {@inheritDoc}
 */
class Filter_Function extends Filter
{
    /**
     * {@inheritDoc}
     */
    public function filter($value)
    {
        $function = $this->getOption(0);

        if (!is_callable($function)) {
            throw new Error('Filter_Function::filter() "%s" is not a function, array(object, function) or closure', $function);
        }

        return call_user_func($function, $value);
    }
}
