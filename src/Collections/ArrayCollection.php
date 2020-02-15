<?php

namespace LaPress\Database\Collections;

use Illuminate\Support\Collection;

/**
 * @author    Sebastian Szczepański
 * @copyright ably
 */
class ArrayCollection extends Collection
{
    /**
     * @param string $key
     * @return ArrayCollection|mixed
     */
    public function __get($key)
    {
        return $this->getCollected($this->get($key));
    }

    /**
     * @param $values
     * @return static
     */
    public function getCollected($values)
    {
        return is_array($values) ? new static($values) : $values;
    }
}
