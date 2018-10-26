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

    private static $subParsers = [
        'character' => Kartigex\Parser\CharacterClass::class,
        'unicode' => Kartigex\Parser\Unicode::class,
        'quantifer' => Kartigex\Parser\Quantifier::class,
        'short' => Kartigex\Parser\Short::class,
    ];

    public function __construct(Lexer $lexer, Generator\Scope $result, Generator\Scope $head = null)
    {
        $this->lexer = $lexer;
        $this->result = $result;
        $this->head = $head ?? new Generator\Scope();
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

                if ($this->lexer->isNextToken(Lexer::GROUP_OPEN)) {
                    # is the group character the first token? is the regex wrapped in brackets.
                    //if($this->lexer->token === null) {
                    //  continue;
                    //}

                    # note this is a new group create new parser instance.
                    $parser = new Parser($this->lexer, new Generator\Scope(), new Generator\Scope());

                    $this->left = $parser->parse()->getResult();
                    $this->head->attach($this->left);
                } elseif ($this->lexer->isNextToken(Lexer::GROUP_CLOSE)) {
                    break;
                } elseif ($this->lexer->isNextTokenAny(array(Lexer::LITERAL_CHAR, Lexer::LITERAL_NUMERIC))) {
                    # test for literal characters (abcd)
                    $this->left = new Kartigex\Generator\LiteralScope();
                    $this->left->addLiteral($this->lexer->lookahead['value']);
                    $this->head->attach($this->left);
                } elseif ($this->lexer->isNextToken(Lexer::SET_OPEN)) {
                    # character classes [a-z]
                    $this->left = new Kartigex\Generator\LiteralScope();
                    self::createSubParser('character')->parse($this->left, $this->head, $this->lexer);
                    $this->head->attach($this->left);
                } elseif ($this->lexer->isNextTokenAny([
                    Lexer::DOT,
                    Lexer::SHORT_D,
                    Lexer::SHORT_NOT_D,
                    Lexer::SHORT_W,
                    Lexer::SHORT_NOT_W,
                    Lexer::SHORT_S,
                    Lexer::SHORT_NOT_S
                ])) {
                    # match short (. \d \D \w \W \s \S)
                    $this->left = new Kartigex\Generator\LiteralScope();
                    self::createSubParser('short')->parse($this->left, $this->head, $this->lexer);
                    $this->head->attach($this->left);
                } elseif ($this->lexer->isNextTokenAny([
                    Lexer::SHORT_P,
                    Lexer::SHORT_UNICODE_X,
                    Lexer::SHORT_X
                ])) {
                    # match short (\p{L} \x \X  )
                    $this->left = new Kartigex\Generator\LiteralScope();
                    self::createSubParser('unicode')->parse($this->left, $this->head, $this->lexer);
                    $this->head->attach($this->left);
                } elseif ($this->lexer->isNextTokenAny([
                    Lexer::QUANTIFIER_OPEN,
                    Lexer::QUANTIFIER_PLUS,
                    Lexer::QUANTIFIER_QUESTION,
                    Lexer::QUANTIFIER_STAR,
                    Lexer::QUANTIFIER_OPEN
                ])) {
                    # match quantifiers
                    self::createSubParser('quantifer')->parse($this->left, $this->head, $this->lexer);
                } elseif ($this->lexer->isNextToken(Lexer::CHOICE_BAR)) {
                    # match alternations
                    $this->left = $this->head;

                    $this->head = new Kartigex\Generator\Scope();
                    $this->result->useAlternatingStrategy();
                    $this->result->attach($this->head);
                }
            }
        } catch (Exception $exception) {
            $position = $this->lexer->lookahead['position'];
            $compressed = $this->compress();
            $message = $exception->getMessage();

            throw new Exception("Error found STARTING at position $position after $compressed with message: $message");
        }

        return $this;
    }

    /**
     * Compress the lexer into value string until current lookahead
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

    /**
     * Return an instance os sub-parser
     *
     * @param string $name
     *
     * @return Parser\StrategyInterface
     * @throws Exception
     */
    static function createSubParser(string $name): Kartigex\Parser\StrategyInterface
    {
        if (!self::$subParsers[$name]) {
            throw new Exception('Unknown sub-parser at ' . $name);
        }

        if (is_object(self::$subParsers[$name]) === false) {
            self::$subParsers[$name] = new self::$subParsers[$name]();
        }

        return self::$subParsers[$name];
    }
}
