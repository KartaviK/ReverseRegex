<?php

namespace Kartavik\Kartigex\Infrastructure;

use Kartavik\Kartigex\Contract;

/**
 * Class BaseRandom
 * @package Kartavik\Kartigex\Infrastructure
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

    public function setMax(int $value): void
    {
        $this->max = $value;
    }

    public function getMax(): int
    {
        return $this->max;
    }
}
