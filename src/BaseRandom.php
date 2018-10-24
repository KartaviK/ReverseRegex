<?php

namespace Kartavik\Kartigex;

/**
 * Class BaseRandom
 * @package Kartavik\Kartigex
 */
abstract class BaseRandom implements Contract\GeneratorInterface
{
    /** @var int */
    protected $seed;

    /** @var int */
    protected $max;

    /** @var int */
    protected $min;

    public function __construct(int $seed = null)
    {
        $this->seed($seed ?? mt_rand(1, PHP_INT_MAX));
    }

    public function getMax(): int
    {
        return $this->max;
    }

    public function setMax(int $max = null): void
    {
        $this->max = $max ?? PHP_INT_MAX;
    }
}
