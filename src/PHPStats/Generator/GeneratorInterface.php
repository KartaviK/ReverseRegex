<?php

namespace PHPStats\Generator;

/**
 * Interface GeneratorInterface
 * @package PHPStats\Generator
 *
 * Interface that all generators should implement
 */
interface GeneratorInterface
{
    /**
     * Generate random number between min and max values
     *
     * @param int $min
     * @param int $max
     *
     * @return int
     */
    public function generate(int $min = 0, int $max = 0): int;

    /**
     * Set the seed to use
     *
     * @param $seed integer the seed to use
     */
    public function seed($seed = null);

    /**
     * Return the highest possible value
     *
     * @return float
     */
    public function max(): float;
}
