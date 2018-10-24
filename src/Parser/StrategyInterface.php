<?php

namespace Kartavik\Kartigex\Parser;

use Kartavik\Kartigex\Generator\Scope;
use Kartavik\Kartigex\Lexer;

/**
 * Interface StrategyInterface
 * @package Kartavik\Kartigex\Parser
 *
 * Interface for all parser strategy object
 *
 * @author Lewis Dyer <getintouch@icomefromthenet.com>
 * @author Roman <KartaviK> Varkuta <roman.varkuta@gmail.com>
 * @since 0.0.1
 */
interface StrategyInterface
{
    /**
     *  Parse the current token and return a new head
     *
     * @access public
     * @return ReverseRegex\Generator\Scope a new head
     *
     * @param ReverseRegex\Generator\Scope $head
     * @param ReverseRegex\Generator\Scope $set
     * @param ReverseRegex\Lexer $lexer
     */
    public function parse(Scope $head, Scope $set, Lexer $lexer);
}
