<?php

namespace Kartavik\Kartigex;

use Kartavik\Kartigex;

/**
 * Class Parser
 * @package Kartavik\Kartigex
 *
 * Parser to convert regex into Group
 *
 * @author Lewis Dyer <getintouch@icomefromthenet.com>
 * @author Roman <KartaviK> Varkuta <roman.varkuta@gmail.com>
 * @since 0.0.1
 */
class Parser
{
    /** @var Lexer */
    protected $lexer;

    /** @var Generator\Scope */
    protected $result;

    /** @var Generator\Scope */
    protected $head;

    /** @var Generator\Scope */
    protected $left;

    public function __construct(Lexer $lexer, Generator\Scope $result, Generator\Scope $head = null)
    {
        $this->lexer = $lexer;
        $this->result = $result;

        if ($head === null) {
            $this->head = new Generator\Scope();
        } else {
            $this->head = $head;
        }

        $this->result->attach($head);

        $this->left = $head;
    }

    /**
     * Fetch the regex lexer
     *
     * @return Lexer
     */
    public function getLexer(): Lexer
    {
        return $this->lexer;
    }

    public function parse()
    {
        try {
            while ($this->lexer->moveNext()) {
                $result = null;
                $scope = null;
                $parser = null;

                if ($this->lexer->isNextToken(Lexer::T_GROUP_OPEN)) {
                    # is the group character the first token? is the regex wrapped in brackets.
                    //if($this->lexer->token === null) {
                    //  continue;
                    //}

                    # note this is a new group create new parser instance.
                    $parser = new Parser($this->lexer, new Generator\Scope(), new Generator\Scope());

                    $this->left = $parser->parse()->getResult();
                    $this->head->attach($this->left);
                } elseif ($this->lexer->isNextToken(Lexer::T_GROUP_CLOSE)) {
                    break;
                } elseif ($this->lexer->isNextTokenAny(array(Lexer::T_LITERAL_CHAR, Lexer::T_LITERAL_NUMERIC))) {
                    # test for literal characters (abcd)
                    $this->left = new Kartigex\Generator\LiteralScope();
                    $this->left->addLiteral($this->lexer->lookahead['value']);
                    $this->head->attach($this->left);
                } elseif ($this->lexer->isNextToken(Lexer::T_SET_OPEN)) {
                    # character classes [a-z]
                    $this->left = new Kartigex\Generator\LiteralScope();
                    self::createSubParser('character')->parse($this->left, $this->head, $this->lexer);
                    $this->head->attach($this->left);
                }

                switch (true) {
                    case ():



                        break;
                    case ($this->lexer->isNextTokenAny(array(
                        Lexer::T_DOT,
                        Lexer::T_SHORT_D,
                        Lexer::T_SHORT_NOT_D,
                        Lexer::T_SHORT_W,
                        Lexer::T_SHORT_NOT_W,
                        Lexer::T_SHORT_S,
                        Lexer::T_SHORT_NOT_S
                    ))):
                        # match short (. \d \D \w \W \s \S)
                        $this->left = new LiteralScope();
                        self::createSubParser('short')->parse($this->left, $this->head, $this->lexer);
                        $this->head->attach($this->left);


                        break;
                    case ($this->lexer->isNextTokenAny(array(
                        Lexer::T_SHORT_P,
                        Lexer::T_SHORT_UNICODE_X,
                        Lexer::T_SHORT_X
                    ))):
                        # match short (\p{L} \x \X  )
                        $this->left = new LiteralScope();
                        self::createSubParser('unicode')->parse($this->left, $this->head, $this->lexer);
                        $this->head->attach($this->left);


                        break;
                    case ($this->lexer->isNextTokenAny(array(
                        Lexer::T_QUANTIFIER_OPEN,
                        Lexer::T_QUANTIFIER_PLUS,
                        Lexer::T_QUANTIFIER_QUESTION,
                        Lexer::T_QUANTIFIER_STAR,
                        Lexer::T_QUANTIFIER_OPEN
                    ))):
                        # match quantifiers
                        self::createSubParser('quantifer')->parse($this->left, $this->head, $this->lexer);

                        break;
                    case ($this->lexer->isNextToken(Lexer::T_CHOICE_BAR)):
                        # match alternations
                        $this->left = $this->head;

                        $this->head = new Scope();
                        $this->result->useAlternatingStrategy();
                        $this->result->attach($this->head);


                        break;
                    default:
                        # ignore character
                }
            }
        } catch (ParserException $e) {
            $pos = $this->lexer->lookahead['position'];
            $compressed = $this->compress();
            throw new ParserException(sprintf('Error found STARTING at position %s after `%s` with msg %s ', $pos,
                $compressed, $e->getMessage()));
        }

        return $this;
    }

    /**
     *  Compress the lexer into value string until current lookahead
     *
     * @access public
     * @return string the compressed value string
     */
    public function compress()
    {
        $current = $this->lexer->lookahead['position'];
        $this->lexer->reset();
        $string = '';

        while ($this->lexer->moveNext() && $this->lexer->lookahead['position'] <= $current) {
            $string .= $this->lexer->lookahead['value'];
        }

        return $string;
    }

    public function getResult()
    {
        return $this->result;
    }

    public static $subParsers = array(
        'character' => '\\ReverseRegex\\Parser\\CharacterClass',
        'unicode' => '\\ReverseRegex\\Parser\\Unicode',
        'quantifer' => '\\ReverseRegex\\Parser\\Quantifier',
        'short' => '\\ReverseRegex\\Parser\\Short'
    );

    /**
     *  Return an instance os subparser
     *
     * @access public
     * @static
     * @return ReverseRegex\Parser\StrategyInterface
     *
     * @param string $name the short name
     */
    static function createSubParser(string $name): StrategyInterface
    {
        if (isset(self::$subParsers[$name]) === false) {
            throw new Exception('Unknown subparser at ' . $name);
        }

        if (is_object(self::$subParsers[$name]) === false) {
            self::$subParsers[$name] = new self::$subParsers[$name]();
        }

        return self::$subParsers[$name];
    }
}
