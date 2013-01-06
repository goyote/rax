<?php

/**
 * {@inheritDoc}
 */
class Filter_Trim extends Filter
{
    /**
     * {@inheritDoc}
     */
    public function filter($value)
    {
        return trim($value);
    }
}
