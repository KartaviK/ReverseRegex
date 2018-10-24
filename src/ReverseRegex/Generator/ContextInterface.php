<?php

namespace ReverseRegex\Generator;

use PHPStats\Generator\GeneratorInterface;

/**
 * Interface ContextInterface
 * @package ReverseRegex\Generator
 *
 * Context interface for Generator
 */
interface ContextInterface
{
    /**
     * Generate a text string appending to result arguments
     *
     * @param string &$result
     * @param GeneratorInterface $generator
     */
    public function generate(string &$result, GeneratorInterface $generator): void;
}
