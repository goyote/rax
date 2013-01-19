<?php

namespace Rax\Data\Base;

use Rax\Data\FileReader;

/**
 * {@inheritDoc}
 */
class BaseConfig extends FileReader
{
    /**
     * {@inheritdoc}
     */
    protected static $dataDir = 'config';
}
