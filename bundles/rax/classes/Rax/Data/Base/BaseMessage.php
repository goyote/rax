<?php

namespace Rax\Data\Base;

use Rax\Data\FileReader;

/**
 * {@inheritDoc}
 */
class BaseMessage extends FileReader
{
    /**
     * {@inheritdoc}
     */
    protected static $dataDir = 'messages';
}
