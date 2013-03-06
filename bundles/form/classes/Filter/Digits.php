<?php

/**
 * {@inheritdoc}
 */
class Filter_Digits extends Filter
{
    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        return preg_replace('/[^0-9]/', '', $value);
    }
}
