<?php

/**
 * Global entity base class. All entities inherit from this class.
 */
class Rax_Entity_Base
{
    /**
     * @param array $data
     *
     * @return $this
     */
    public function update(array $data)
    {
        foreach ($data as $key => $value) {
            $property = Inflector::toCamelcase($key);

            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }

        return $this;
    }
}
