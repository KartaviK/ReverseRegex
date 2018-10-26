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
     * @param Kartigex\Generator\Scope $head
     * @param Kartigex\Generator\Scope $set
     * @param Kartigex\Lexer $lexer
     *
     * @return string
     * @throws Kartigex\Exception
     */
    public function normalize(
        Kartigex\Generator\Scope $head,
        Kartigex\Generator\Scope $set,
        Kartigex\Lexer $lexer
    ): string {
        $collection = [];
        $unicode = new Unicode();

        while ($lexer->moveNext() && !$lexer->isNextToken(Kartigex\Lexer::SET_CLOSE)) {
            $value = null;

            if ($lexer->isNextTokenAny([
                Kartigex\Lexer::SHORT_UNICODE_X,
                Kartigex\Lexer::SHORT_P,
                Kartigex\Lexer::SHORT_X
            ])) {
                $collection[] = $unicode->evaluate($lexer);
            } elseif ($lexer->isNextTokenAny([
                Kartigex\Lexer::LITERAL_CHAR,
                Kartigex\Lexer::LITERAL_NUMERIC
            ])) {
                $collection[] = $lexer->lookahead['value'];
            } elseif ($lexer->isNextToken(Kartigex\Lexer::SET_RANGE)) {
                $collection[] = '-';
            } elseif ($lexer->isNextToken(Kartigex\Lexer::ESCAPE_CHAR)) {
                $collection[] = '\\';
            } else {
                throw new Kartigex\Exception('Illegal meta character detected in character class');
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
     * @param Kartigex\Generator\Scope $head
     * @param Kartigex\Generator\Scope $set
     * @param Kartigex\Lexer $lexer
     *
     * @return Kartigex\Generator\Scope
     * @throws Kartigex\Exception
     */
    public function parse(
        Kartigex\Generator\Scope $head,
        Kartigex\Generator\Scope $set,
        Kartigex\Lexer $lexer
    ): Kartigex\Generator\Scope {
        if ($lexer->lookahead['type'] !== Kartigex\Lexer::SET_OPEN) {
            throw new Kartigex\Exception('Opening character set token not found');
        }

        $peek = $lexer->glimpse();
        if ($peek['type'] === Kartigex\Lexer::SET_NEGATED) {
            throw new Kartigex\Exception('Negated Character Set ranges not supported at this time');
        }

        $normalLexer = new Kartigex\Lexer($this->normalize($head, $set, $lexer));

        while ($normalLexer->moveNext() && !$normalLexer->isNextToken(Kartigex\Lexer::SET_CLOSE)) {
            $glimpse = $normalLexer->glimpse();

            if ($glimpse['type'] === Kartigex\Lexer::SET_RANGE) {
                continue; //value be included in range when `-` character is passed
            }

            if ($normalLexer->isNextToken(Kartigex\Lexer::SET_RANGE)) {
                $range_start = $normalLexer->token['value'];

                $normalLexer->moveNext();

                if ($normalLexer->isNextToken(Kartigex\Lexer::ESCAPE_CHAR)) {
                    $normalLexer->moveNext();
                }

                $range_end = $normalLexer->lookahead['value'];
                $this->fillRange($head, $range_start, $range_end);
            } elseif ($normalLexer->isNextToken(Kartigex\Lexer::LITERAL_NUMERIC)
                || $normalLexer->isNextToken(Kartigex\Lexer::LITERAL_CHAR)) {
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
     * @param Kartigex\Generator\Scope $head
     * @param int $start
     * @param int $end
     *
     * @throws Kartigex\Exception
     */
    public function fillRange(Kartigex\Generator\Scope $head, int $start, int $end): void
    {
        $start = Utf8::ord($start);
        $end = Utf8::ord($end);

        if ($end < $start) {
            throw new Kartigex\Exception(sprintf('Character class range %s - %s is out of order', $start, $end));
        }

        for ($i = $start; $i <= $end; $i++) {
            $head->setLiteral($i, Utf8::chr($i));
        }
    }
}
