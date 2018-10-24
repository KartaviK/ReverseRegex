<?php

namespace Kartavik\Kartigex\Random;

/**
 * Class Simple
 * @package Kartavik\Kartigex\Random
 *
 * @link http://www.sitepoint.com/php-random-number-generator/
 * @author Craig Buckler
 */
class Simple implements GeneratorInterface
{
    public function __construct(int $seed = null)
    {
        $this->seed($seed ?? mt_rand(1, PHP_INT_MAX));
    }

    public function max(int $value = null): int
    {
        if ($value === null && $this->max === null) {
            return PHP_INT_MAX;
        } elseif ($value === null) {
            return $this->max;
        }

        return $this->max = $value;
    }

    public function min(int $value = null): int
    {
        if ($value === null && $this->max === null) {
            return 0;
        } elseif ($value === null) {
            return $this->min;
        }

        return $this->min = $value;
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
