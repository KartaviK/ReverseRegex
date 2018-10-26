<?php

namespace Kartavik\Kartigex\Contract;

/**
 * Interface GeneratorInterface
 * @package Kartavik\Kartigex\Contract
 *
 * Interface that all generators should implement
 *
 * @author Lewis Dyer <getintouch@icomefromthenet.com>
 * @author Roman <KartaviK> Varkuta <roman.varkuta@gmail.com>
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
    public function generate(int $min = 0, int $max = PHP_INT_MAX): int;

    /**
     * Set the seed to use
     *
     * @param int $seed integer the seed to use
     */
    public function seed(int $seed): void;

    public function setMax(int $value);

    public function getMax(): int;

    public function setMin(int $value): void;

    public function getMin(): int;
}
