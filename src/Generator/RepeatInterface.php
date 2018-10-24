<?php

namespace Kartavik\Kartigex\Generator;

/**
 * Interface RepeatInterface
 * @package Kartavik\Kartigex\Generator
 *
 * Represent a group has max and min number of occurrences
 *
 * @author Lewis Dyer <getintouch@icomefromthenet.com>
 * @since 0.0.1
 * @author Roman <KartaviK> Varkuta <roman.varkuta@gmail.com>
 */
interface RepeatInterface
{
    /**
     * Fetches the max re-occurrences
     *
     * @return int The max number of occurrences
     */
    public function getMaxOccurrences(): int;

    /**
     * Set the max re-occurrences
     *
     * @param int $number
     */
    public function setMaxOccurrences(int $number): void;

    /**
     * Fetch the Minimum re-occurrences
     *
     * @return int
     */
    public function getMinOccurrences(): int;

    /**
     * Sets the Minimum number of re-occurrences
     *
     * @param int $num
     */
    public function setMinOccurrences(int $num): void;

    /**
     * Return the occurrence range
     *
     * @return int the range
     */
    public function getOccurrenceRange(): int;
}
