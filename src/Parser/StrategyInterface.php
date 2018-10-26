<?php

namespace Kartavik\Kartigex\Parser;

use Kartavik\Kartigex;

/**
 * Interface StrategyInterface
 * @package Kartavik\Kartigex\Parser
 *
 * Interface for all parser strategy object
 *
 * @author Lewis Dyer <getintouch@icomefromthenet.com>
 * @since 0.0.1
 * @author Roman <KartaviK> Varkuta <roman.varkuta@gmail.com>
 */
interface StrategyInterface
{
    /**
     * Parse the current token and return a new head
     *
     * @param Kartigex\Generator\Scope $head
     * @param Kartigex\Generator\Scope $set
     * @param Kartigex\Lexer $lexer
     *
     * @return mixed
     */
    public function parse(Kartigex\Generator\Scope $head, Kartigex\Generator\Scope $set, Kartigex\Lexer $lexer);
}
