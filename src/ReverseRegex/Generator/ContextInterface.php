<?php

namespace ReverseRegex\Generator;

use PHPStats\Generator\GeneratorInterface;

/**
 * Interface ContextInterface
 * @package ReverseRegex\Generator
 *
 * Context interface for Generator
 *
 * @author Lewis Dyer <getintouch@icomefromthenet.com>
 * @since 0.0.1
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
