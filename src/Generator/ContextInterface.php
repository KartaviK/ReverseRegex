<?php

namespace Kartavik\Kartigex\Generator;

use Kartavik\Kartigex\Random;

/**
 * Interface ContextInterface
 * @package Kartavik\Kartigex\Generator
 *
 * Context interface for Generator
 *
 * @author Lewis Dyer <getintouch@icomefromthenet.com>
 * @author Roman <KartaviK> Varkuta <roman.varkuta@gmail.com>
 * @since 0.0.1
 */
interface ContextInterface
{
    /**
     * Generate a text string appending to result arguments
     *
     * @param string &$result
     * @param Random\GeneratorInterface $generator
     */
    public function generate(string &$result, Random\GeneratorInterface $generator): void;
}
