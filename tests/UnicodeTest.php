<?php

namespace Kartavik\Kartigex\Test;

use Kartavik\Kartigex;
use PHPUnit\Framework\TestCase;

/**
 * Class UnicodeTest
 * @package Kartavik\Kartigex\Test
 * @internal
 */
class UnicodeTest extends TestCase
{
    /**
     * @expectedException \Kartavik\Kartigex\Exception
     * @expectedExceptionMessage Property \p (Unicode Property) not supported use \x to specify unicode character or
     *     range
     */
    public function testUnsupportedShortProperty(): void
    {
        $lexer = new Kartigex\Lexer('\p');
        $scope = new Kartigex\Generator\Scope();
        $parser = new Kartigex\Parser\Unicode();

        $lexer->moveNext();
        $lexer->moveNext();

        $parser->parse($scope, $scope, $lexer);
    }

    /**
     * @expectedException \Kartavik\Kartigex\Exception
     * @expectedExceptionMessage Expecting character { after \X none found
     */
    public function testErrorNoOpeningBrace(): void
    {
        $lexer = new Kartigex\Lexer('\Xaaaaa');
        $scope = new Kartigex\Generator\Scope();
        $parser = new Kartigex\Parser\Unicode();

        $lexer->moveNext();
        $lexer->moveNext();

        $parser->parse($scope, $scope, $lexer);
    }

    /**
     * @expectedException \Kartavik\Kartigex\Exception
     * @expectedExceptionMessage Nesting hex value ranges is not allowed
     */
    public function testErrorNested(): void
    {
        $lexer = new Kartigex\Lexer('\X{aa{aa}');
        $scope = new Kartigex\Generator\Scope();
        $parser = new Kartigex\Parser\Unicode();

        $lexer->moveNext();
        $lexer->moveNext();

        $parser->parse($scope, $scope, $lexer);
    }

    /**
     * @expectedException \Kartavik\Kartigex\Exception
     * @expectedExceptionMessage Closing quantifier token `}` not found
     */
    public function testErrorUnclosed(): void
    {
        $lexer = new Kartigex\Lexer('\X{aaaa');
        $scope = new Kartigex\Generator\Scope();
        $parser = new Kartigex\Parser\Unicode();

        $lexer->moveNext();
        $lexer->moveNext();

        $parser->parse($scope, $scope, $lexer);
    }

    /**
     * @expectedException \Kartavik\Kartigex\Exception
     * @expectedExceptionMessage No hex number found inside the range
     */
    public function testErrorEmptyToken(): void
    {
        $lexer = new Kartigex\Lexer('\X{}');
        $scope = new Kartigex\Generator\Scope();
        $parser = new Kartigex\Parser\Unicode();

        $lexer->moveNext();
        $lexer->moveNext();

        $parser->parse($scope, $scope, $lexer);
    }

    public function testsExampleA(): void
    {
        $lexer = new Kartigex\Lexer('\X{FA24}');
        $scope = new Kartigex\Generator\Scope();
        $parser = new Kartigex\Parser\Unicode();
        $head = new Kartigex\Generator\LiteralScope('lit1', $scope);

        $lexer->moveNext();
        $lexer->moveNext();

        $parser->parse($head, $scope, $lexer);

        $result = $head->getLiterals();

        $this->assertEquals('﨤', $result[0]);
    }

    /**
     * @expectedException \Kartavik\Kartigex\Exception
     * @expectedExceptionMessage Braces not supported here
     */
    public function testShortErrorWhenBraces(): void
    {
        $lexer = new Kartigex\Lexer('\x{64');
        $scope = new Kartigex\Generator\Scope();
        $parser = new Kartigex\Parser\Unicode();
        $head = new Kartigex\Generator\LiteralScope('lit1', $scope);

        $lexer->moveNext();
        $lexer->moveNext();

        $parser->parse($head, $scope, $lexer);

        $result = $head->getLiterals();

        $this->assertEquals('d', $result[0]);
    }

    public function testShortX(): void
    {
        $lexer = new Kartigex\Lexer('\x64');
        $scope = new Kartigex\Generator\Scope();
        $parser = new Kartigex\Parser\Unicode();
        $head = new Kartigex\Generator\LiteralScope('lit1', $scope);

        $lexer->moveNext();
        $lexer->moveNext();

        $parser->parse($head, $scope, $lexer);

        $result = $head->getLiterals();

        $this->assertEquals('d', $result[0]);
    }
}
