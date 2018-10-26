<?php

namespace Kartavik\Kartigex\Random;

/**
 * Class Simple
 * @package Kartavik\Kartigex\Random
 *
 * @link http://www.sitepoint.com/php-random-number-generator/
 * @author Craig Buckler
 */
class Simple implem
{
    public function __construct(int $seed = null)
    {
        $this->seed($seed ?? mt_rand(1, PHP_INT_MAX));
    }

    /**
     * Set the seed to use for generator
     *
     * @param int $seed the seed to use
     */
    public function seed(int $seed = 0): void
    {
        $this->seed = abs(intval($seed)) % 9999999 + 1;
    }

    /**
     * Generate a random number
     *
     * @param int $min
     * @param int $max
     *
     * @return int
     */
    public function generate(int $min = 0, int $max = PHP_INT_MAX): int
    {
        if ($this->seed == 0) {
            $this->seed(mt_rand());
        }

        $this->seed = ($this->seed * 125) % 2796203;

        return $this->seed % ($max - $min + 1) + $min;
    }
}
