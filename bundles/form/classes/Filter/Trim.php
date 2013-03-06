<?php

/**
 * {@inheritdoc}
 */
class Filter_Trim extends Filter
{
    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        return trim($value);
    }
}
