<?php

namespace ReverseRegex\Generator;

/**
 * Interface RepeatInterface
 * @package ReverseRegex\Generator
 *
 * Represent a group has max and min number of occurrences
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
     * @param $num
     *
     * @return int
     */
    public function setMaxOccurrences($num): int;

    /**
     * Fetch the Minimum re-occurrences
     *
     * @return int
     */
    public function getMinOccurrences(): int;

    /**
     * Sets the Minimum number of re-occurrences
     *
     * @param integer $num
     */
    public function setMinOccurrences($num): void;

    /**
     * Return the occurrence range
     *
     * @return int the range
     */
    public function getOccurrenceRange(): int;
}
