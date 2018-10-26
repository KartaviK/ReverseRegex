<?php

namespace Kartavik\Kartigex\Random;

use Kartavik\Kartigex;

/**
 * Class SrandRandom
 * @package Kartavik\Kartigex\Random
 *
 * Wrapper to mt_random with seed option
 * Won't work when suhosin.srand.ignore = Off or suhosin.mt_srand.ignore = Off
 * is set.
 *
 * @author Lewis Dyer <getintouch@icomefromthenet.com>
 * @author Roman <KartaviK> Varkuta <roman.varkuta@gmail.com>
 */
class Srand extends Kartigex\BaseRandom
{
    /** @var integer the seed to use on each pass */
    protected $seed;

    /** @var integer the max */
    protected $max;

    /** @var integer the min */
    protected $min;

    public function __construct(int $seed = 0)
    {
        $this->seed($seed);
    }

    /**
     * {@inheritdoc}
     */
    public function max(int $value = PHP_INT_MAX): int
    {
        if ($value === null && $this->max === null) {
            $max = getrandmax();
        } elseif ($value === null) {
            $max = $this->max;
        } else {
            $max = $this->max = $value;
        }

        return $max;
    }


    public function min($value = null)
    {
        if ($value === null && $this->max === null) {
            $min = 0;
        } elseif ($value === null) {
            $min = $this->min;
        } else {
            $min = $this->min = $value;
        }

        return $min;
    }

    public function generate(int $min = 0, int $max = PHP_INT_MAX): int
    {
        if ($max === null) {
            $max = $this->max;
        }

        if ($min === null) {
            $min = $this->min;
        }

        return rand($min, $max);
    }

    /**
     * {@inheritdoc}
     */
    public function seed(int $seed = 0): void
    {
        mt_srand($this->seed = $seed);
    }
}
