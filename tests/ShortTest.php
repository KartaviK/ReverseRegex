<?php

namespace Kartavik\Kartigex\Test;

use Kartavik\Kartigex;
use PHPUnit\Framework\TestCase;

/**
 * Class ShortTest
 * @package ReverseRegex\Test
 * @internal
 */
class ShortTest extends TestCase
{
    public function testDigit(): void
    {
        $lexer = new Kartigex\Lexer('\d');
        $scope = new Kartigex\Generator\Scope();
        $parser = new Kartigex\Parser\Short();
        $head = new Kartigex\Generator\LiteralScope('lit1', $scope);

        $lexer->moveNext();
        $lexer->moveNext();

        $parser->parse($head, $scope, $lexer);

        $result = $head->getLiterals();

        foreach ($result as $value) {
            $this->assertRegExp('/\d/', $value);
        }
    }

    public function testNotDigit(): void
    {
        $lexer = new Kartigex\Lexer('\D');
        $scope = new Kartigex\Generator\Scope();
        $parser = new Kartigex\Parser\Short();
        $head = new Kartigex\Generator\LiteralScope('lit1', $scope);

        $lexer->moveNext();
        $lexer->moveNext();

        $parser->parse($head, $scope, $lexer);

        $result = $head->getLiterals();

        foreach ($result as $value) {
            $this->assertRegExp('/\D/', $value);
        }
    }

    public function testWhitespace(): void
    {
        $lexer = new Kartigex\Lexer('\s');
        $scope = new Kartigex\Generator\Scope();
        $parser = new Kartigex\Parser\Short();
        $head = new Kartigex\Generator\LiteralScope('lit1', $scope);

        $lexer->moveNext();
        $lexer->moveNext();

        $parser->parse($head, $scope, $lexer);

        $result = $head->getLiterals();

        foreach ($result as $value) {
            $this->assertTrue(!empty($value));
        }
    }

    public function testNonWhitespace(): void
    {
        $lexer = new Kartigex\Lexer('\S');
        $scope = new Kartigex\Generator\Scope();
        $parser = new Kartigex\Parser\Short();
        $head = new Kartigex\Generator\LiteralScope('lit1', $scope);

        $lexer->moveNext();
        $lexer->moveNext();

        $parser->parse($head, $scope, $lexer);

        $result = $head->getLiterals();

        foreach ($result as $value) {
            $this->assertTrue(!empty($value));
        }
    }

    public function testWord(): void
    {
        $lexer = new Kartigex\Lexer('\w');
        $scope = new Kartigex\Generator\Scope();
        $parser = new Kartigex\Parser\Short();
        $head = new Kartigex\Generator\LiteralScope('lit1', $scope);

        $lexer->moveNext();
        $lexer->moveNext();

        $parser->parse($head, $scope, $lexer);

        $result = $head->getLiterals();

        foreach ($result as $value) {
            $this->assertRegExp('/\w/', $value);
        }
    }

    public function testNonWord(): void
    {
        $lexer = new Kartigex\Lexer('\W');
        $scope = new Kartigex\Generator\Scope();
        $parser = new Kartigex\Parser\Short();
        $head = new Kartigex\Generator\LiteralScope('lit1', $scope);

        $lexer->moveNext();
        $lexer->moveNext();

        $parser->parse($head, $scope, $lexer);

        $result = $head->getLiterals();

        foreach ($result as $value) {
            $this->assertRegExp('/\W/', $value);
        }
    }

    public function testDotRange(): void
    {
        $lexer = new Kartigex\Lexer('.');
        $scope = new Kartigex\Generator\Scope();
        $parser = new Kartigex\Parser\Short();
        $head = new Kartigex\Generator\LiteralScope('lit1', $scope);

        $lexer->moveNext();

        $parser->parse($head, $scope, $lexer);

        $result = $head->getLiterals();

        // match 0..127 char in ASSCI Chart
        $this->assertCount(128, $result);
    }
}
