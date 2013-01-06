<?php

/**
 * {@inheritDoc}
 */
class Filter_Digits extends Filter
{
    /**
     * {@inheritDoc}
     */
    public function filter($value)
    {
        return preg_replace('/[^0-9]/', '', $value);
    }
}
