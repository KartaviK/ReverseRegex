<?php

namespace Kartavik\Kartigex;

use Doctrine\Common;

/**
 * Class Lexer
 * @package Kartavik\Kartigex
 *
 * Lexer to split expression syntax
 *
 * @author Lewis Dyer <getintouch@icomefromthenet.com>
 * @author Roman <KartaviK> Varkuta <roman.varkuta@gmail.com>
 * @since 0.0.1
 */
class Lexer extends Common\Lexer\AbstractLexer
{
    /**
     * An escape character
     */
    const ESCAPE_CHAR = -1;

    /**
     * The literal type ie a=a ^=^
     */
    const LITERAL_CHAR = 0;

    /**
     * Numeric literal  1=1 100=100
     */
    const LITERAL_NUMERIC = 1;

    /**
     * The opening character for group. [(]
     */
    const GROUP_OPEN = 2;

    /**
     * The closing character for group  [)]
     */
    const GROUP_CLOSE = 3;

    /**
     * Opening character for Quantifier  ({)
     */
    const QUANTIFIER_OPEN = 4;

    /**
     * Closing character for Quantifier (})
     */
    const QUANTIFIER_CLOSE = 5;

    /**
     * Star quantifier character (*)
     */
    const QUANTIFIER_STAR = 6;

    /**
     * Plus quantifier character (+)
     */
    const QUANTIFIER_PLUS = 7;

    /**
     * The one but optional character (?)
     */
    const QUANTIFIER_QUESTION = 8;

    /**
     * Start of string character (^)
     */
    const START_CARET = 9;

    /**
     * End of string character ($)
     */
    const END_DOLLAR = 10;

    /**
     * Range character inside set ([)
     */
    const SET_OPEN = 11;

    /**
     * Range character inside set (])
     */
    const SET_CLOSE = 12;

    /**
     * Range character inside set (-)
     */
    const SET_RANGE = 13;

    /**
     * Negated Character in set ([^)
     */
    const SET_NEGATED = 14;

    /**
     * The either character (|)
     */
    const CHOICE_BAR = 15;

    /**
     * The dot character (.)
     */
    const DOT = 16;

    /**
     * One Word boundary
     */
    const SHORT_W = 100;
    const SHORT_NOT_W = 101;

    const SHORT_D = 102;
    const SHORT_NOT_D = 103;

    const SHORT_S = 104;
    const SHORT_NOT_S = 105;

    /**
     * Unicode sequences /p{} /pNum
     */
    const SHORT_P = 106;

    /**
     * Hex Sequences /x{} /xNum
     */
    const SHORT_X = 108;

    /**
     * Unicode hex sequence /X{} /XNum
     */
    const SHORT_UNICODE_X = 109;

    /** @var bool The lexer has detected escape character */
    protected $escapeMode = false;

    /** @var bool The lexer is parsing a char set */
    protected $setMode = false;

    /** @var int The number of groups open */
    protected $groupSet = 0;

    /** @var int Number of characters parsed inside the set */
    protected $setInternalCounter = 0;

    /**
     * Creates a new query scanner object.
     *
     * @param string $input a query string
     */
    public function __construct($input)
    {
        $this->setInput($input);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCatchablePatterns(): array
    {
        return ['.',];
    }

    /**
     * {@inheritdoc}
     */
    protected function getNonCatchablePatterns(): array
    {
        return ['\s+',];
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    protected function getType(&$value): int
    {
        $type = null;

        switch (true) {
            case ($value === '\\' && $this->escapeMode === false):
                $this->escapeMode = true;
                $type = static::ESCAPE_CHAR;

                if ($this->setMode === true) {
                    $this->setInternalCounter++;
                }

                break;
            // Groups
            case ($value === '(' && $this->escapeMode === false && $this->setMode === false):
                $type = static::GROUP_OPEN;
                $this->groupSet++;

                break;
            case ($value === ')' && $this->escapeMode === false && $this->setMode === false):
                $type = static::GROUP_CLOSE;
                $this->groupSet--;

                break;
            // Charset
            case ($value === '[' && $this->escapeMode === false && $this->setMode === true):
                throw new Exception("Can't have a second character class while first remains open");
            case ($value === ']' && $this->escapeMode === false && $this->setMode === false):
                throw new Exception("Can't close a character class while none is open");
            case ($value === '[' && $this->escapeMode === false && $this->setMode === false):
                $this->setMode = true;
                $type = static::SET_OPEN;
                $this->setInternalCounter = 1;

                break;
            case ($value === ']' && $this->escapeMode === false && $this->setMode === true):
                $this->setMode = false;
                $type = static::SET_CLOSE;
                $this->setInternalCounter = 0;

                break;
            case ($value === '-' && $this->escapeMode === false && $this->setMode === true):
                $this->setInternalCounter++;

                return static::SET_RANGE;
            case ($value === '^' && $this->escapeMode === false
                && $this->setMode === true && $this->setInternalCounter === 1):
                $this->setInternalCounter++;

                return static::SET_NEGATED;
            // Quantifers
            case ($value === '{' && $this->escapeMode === false && $this->setMode === false):
                return static::QUANTIFIER_OPEN;
            case ($value === '}' && $this->escapeMode === false && $this->setMode === false):
                return static::QUANTIFIER_CLOSE;
            case ($value === '*' && $this->escapeMode === false && $this->setMode === false):
                return static::QUANTIFIER_STAR;
            case ($value === '+' && $this->escapeMode === false && $this->setMode === false):
                return static::QUANTIFIER_PLUS;
            case ($value === '?' && $this->escapeMode === false && $this->setMode === false):
                return static::QUANTIFIER_QUESTION;
            // Recognize symbols
            case ($value === '.' && $this->escapeMode === false && $this->setMode === false):
                return static::DOT;
            case ($value === '|' && $this->escapeMode === false && $this->setMode === false):
                return static::CHOICE_BAR;
            case ($value === '^' && $this->escapeMode === false && $this->setMode === false):
                return static::START_CARET;
            case ($value === '$' && $this->escapeMode === false && $this->setMode === false):
                return static::END_DOLLAR;
            // ShortCodes
            case ($value === 'd' && $this->escapeMode === true):
                $type = static::SHORT_D;
                $this->escapeMode = false;

                break;
            case ($value === 'D' && $this->escapeMode === true):
                $type = static::SHORT_NOT_D;
                $this->escapeMode = false;

                break;
            case ($value === 'w' && $this->escapeMode === true):
                $type = static::SHORT_W;
                $this->escapeMode = false;

                break;
            case ($value === 'W' && $this->escapeMode === true):
                $type = static::SHORT_NOT_W;
                $this->escapeMode = false;

                break;
            case ($value === 's' && $this->escapeMode === true):
                $type = static::SHORT_S;
                $this->escapeMode = false;

                break;
            case ($value === 'S' && $this->escapeMode === true):
                $type = static::SHORT_NOT_S;
                $this->escapeMode = false;

                break;
            case ($value === 'x' && $this->escapeMode === true):
                $type = static::SHORT_X;
                $this->escapeMode = false;

                if ($this->setMode === true) {
                    $this->setInternalCounter++;
                }

                break;
            case ($value === 'X' && $this->escapeMode === true):
                $type = static::SHORT_UNICODE_X;
                $this->escapeMode = false;

                if ($this->setMode === true) {
                    $this->setInternalCounter++;
                }

                break;
            case (($value === 'p' || $value === 'P') && $this->escapeMode === true):
                $type = static::SHORT_P;
                $this->escapeMode = false;

                if ($this->setMode === true) {
                    $this->setInternalCounter++;
                }

                break;
            // Default
            default:
                if (is_numeric($value) === true) {
                    $type = static::LITERAL_NUMERIC;
                } else {
                    $type = static::LITERAL_CHAR;
                }

                if ($this->setMode === true) {
                    $this->setInternalCounter++;
                }

                $this->escapeMode = false;
        }

        return $type;
    }

    /**
     * Scans the input string for tokens.
     *
     * @param string $input
     *
     * @throws Exception
     */
    protected function scan($input)
    {
        # reset default for scan
        $this->groupSet = 0;
        $this->escapeMode = false;
        $this->setMode = false;

        static $regex;

        if (!isset($regex)) {
            $regex =
                '/('
                . implode(')|(', $this->getCatchablePatterns())
                . ')|'
                . implode('|', $this->getNonCatchablePatterns())
                . '/ui';
        }

        parent::scan($input);

        if ($this->groupSet > 0) {
            throw new Exception('Opening group char "(" has no matching closing character');
        }

        if ($this->groupSet < 0) {
            throw new Exception('Closing group char "(" has no matching opening character');
        }

        if ($this->setMode === true) {
            throw new Exception('Character Class that been closed');
        }
    }
}
