<?php

namespace Kartavik\Kartigex\Generator;

use Kartavik\Kartigex\Contract\GeneratorInterface;

/**
 * Interface ContextInterface
 * @package Kartavik\Kartigex\Generator
 *
 * Context interface for Generator
 *
 * @author Lewis Dyer <getintouch@icomefromthenet.com>
 * @since 0.0.1
 * @author Roman <KartaviK> Varkuta <roman.varkuta@gmail.com>
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
