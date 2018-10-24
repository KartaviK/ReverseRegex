<?php

namespace Kartavik\Kartigex;

use Doctrine\Common\Collections\ArrayCollection as BaseCollection;

/**
 * Class ArrayCollection
 * @package Kartavik\Kartigex
 */
class ArrayCollection extends BaseCollection
{
    /**
     * Sort the values using a ksort
     *
     * @return ArrayCollection
     */
    public function sort()
    {
        $values = $this->toArray();
        ksort($values);

        $this->clear();

        foreach ($values as $index => $value) {
            $this->set($index, $value);
        }

        return $this;
    }

    /**
     *  Fetch a value using ones based position
     *
     * @access public
     *
     * @param integer $position
     *
     * @return mixed | null if bad position
     */
    public function getAt($position)
    {
        if ($position < $this->count() && $position < 0) {
            return null;
        }

        $this->first();

        while ($position > 1) {
            $this->next();
            --$position;
        }

        return $this->current();
    }
}
