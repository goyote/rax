<?php

/**
 *
 */
class Core_Config_Group extends ArrObj
{
    /**
     * @var string
     */
    protected $groupName;

    /**
     * @param string $groupName
     * @param array $config
     */
    public function __construct($groupName, $config = array())
    {
        $this->groupName = $groupName;

        parent::__construct($config, ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @return string
     */
    public function groupName()
    {
        return $this->groupName;
    }
}
