<?php

namespace Kartavik\Kartigex\Parser;

use Kartavik\Kartigex;
use Patchwork\Utf8;

/**
 * Class CharacterClass
 * @package Kartavik\Kartigex\Parser
 *
 * Parse a character class [0-9][a-z]
 *
 * @author Lewis Dyer <getintouch@icomefromthenet.com>
 * @since 0.0.1
 * @author Roman <KartaviK> Varkuta <roman.varkuta@gmail.com>
 */
class CharacterClass implements StrategyInterface
{
    /**
     * Will return a normalized ie unicode sequences been evaluated.
     *
     * @param ReverseRegex\Generator\Scope $head
     * @param ReverseRegex\Generator\Scope $set
     * @param ReverseRegex\Lexer $lexer
     *
     * @return string
     * @throws ReverseRegex\Exception
     */
    public function normalize(
        ReverseRegex\Generator\Scope $head,
        ReverseRegex\Generator\Scope $set,
        ReverseRegex\Lexer $lexer
    ): string {
        $collection = [];
        $unicode = new Unicode();

        while ($lexer->moveNext() && !$lexer->isNextToken(ReverseRegex\Lexer::T_SET_CLOSE)) {
            $value = null;

            if ($lexer->isNextTokenAny([
                ReverseRegex\Lexer::T_SHORT_UNICODE_X,
                ReverseRegex\Lexer::T_SHORT_P,
                ReverseRegex\Lexer::T_SHORT_X
            ])) {
                $collection[] = $unicode->evaluate($lexer);
            } elseif ($lexer->isNextTokenAny([
                ReverseRegex\Lexer::T_LITERAL_CHAR,
                ReverseRegex\Lexer::T_LITERAL_NUMERIC
            ])) {
                $collection[] = $lexer->lookahead['value'];
            } elseif ($lexer->isNextToken(ReverseRegex\Lexer::T_SET_RANGE)) {
                $collection[] = '-';
            } elseif ($lexer->isNextToken(ReverseRegex\Lexer::T_ESCAPE_CHAR)) {
                $collection[] = '\\';
            } else {
                throw new ReverseRegex\Exception('Illegal meta character detected in character class');
            }
        }

        /*
        if($lexer->lookahead['type'] === null) {
            throw new ParserException('Closing character set token not found');
        } */

        return '[' . implode('', $collection) . ']';
    }

    /**
     * Parse the current token for new Quantifiers
     *
     * @param ReverseRegex\Generator\Scope $head
     * @param ReverseRegex\Generator\Scope $set
     * @param ReverseRegex\Lexer $lexer
     *
     * @return ReverseRegex\Generator\Scope
     * @throws ReverseRegex\Exception
     */
    public function parse(
        ReverseRegex\Generator\Scope $head,
        ReverseRegex\Generator\Scope $set,
        ReverseRegex\Lexer $lexer
    ): ReverseRegex\Generator\Scope {
        if ($lexer->lookahead['type'] !== ReverseRegex\Lexer::T_SET_OPEN) {
            throw new ReverseRegex\Exception('Opening character set token not found');
        }

        $peek = $lexer->glimpse();
        if ($peek['type'] === ReverseRegex\Lexer::T_SET_NEGATED) {
            throw new ReverseRegex\Exception('Negated Character Set ranges not supported at this time');
        }

        $normalLexer = new ReverseRegex\Lexer($this->normalize($head, $set, $lexer));

        while ($normalLexer->moveNext() && !$normalLexer->isNextToken(ReverseRegex\Lexer::T_SET_CLOSE)) {
            $glimpse = $normalLexer->glimpse();

            if ($glimpse['type'] === ReverseRegex\Lexer::T_SET_RANGE) {
                continue; //value be included in range when `-` character is passed
            }

            if ($normalLexer->isNextToken(ReverseRegex\Lexer::T_SET_RANGE)) {
                $range_start = $normalLexer->token['value'];

                $normalLexer->moveNext();

                if ($normalLexer->isNextToken(ReverseRegex\Lexer::T_ESCAPE_CHAR)) {
                    $normalLexer->moveNext();
                }

                $range_end = $normalLexer->lookahead['value'];
                $this->fillRange($head, $range_start, $range_end);
            } elseif ($normalLexer->isNextToken(ReverseRegex\Lexer::T_LITERAL_NUMERIC)
                || $normalLexer->isNextToken(Lexer::T_LITERAL_CHAR)) {
                $index = (integer)Utf8::ord($normalLexer->lookahead['value']);
                $head->setLiteral($index, $normalLexer->lookahead['value']);
            }
        }

        $head->getLiterals()->sort();

        return $head;
    }

    /**
     * Fill a range given starting and ending character
     *
     * @param ReverseRegex\Generator\Scope $head
     * @param int $start
     * @param int $end
     *
     * @throws ReverseRegex\Exception
     */
    public function fillRange(ReverseRegex\Generator\Scope $head, int $start, int $end): void
    {
        $start = Utf8::ord($start);
        $end = Utf8::ord($end);

        if ($end < $start) {
            throw new ReverseRegex\Exception(sprintf('Character class range %s - %s is out of order', $start, $end));
        }

        for ($i = $start; $i <= $end; $i++) {
            $head->setLiteral($i, Utf8::chr($i));
        }
    }
}
