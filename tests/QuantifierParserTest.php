<?php

namespace Kartavik\Kartigex\Test;

use Kartavik\Kartigex;
use PHPUnit\Framework\TestCase;

/**
 * Class QuantifierParserTest
 * @package ReverseRegex\Test
 * @internal
 */
class QuantifierParserTest extends TestCase
{
    public function testQuantifierParserPatternA(): void
    {
        $pattern = '{1,5}';
        $lexer = new Kartigex\Lexer($pattern);
        $scope = new Kartigex\Generator\Scope();
        $qual = new Kartigex\Parser\Quantifier();

        $lexer->moveNext();
        $qual->parse($scope, $scope, $lexer);

        $this->assertEquals(1, $scope->getMinOccurrences());
        $this->assertEquals(5, $scope->getMaxOccurrences());
    }

    public function testQuantifierSingleValue(): void
    {
        $pattern = '{5}';
        $lexer = new Kartigex\Lexer($pattern);
        $scope = new Kartigex\Generator\Scope();
        $qual = new Kartigex\Parser\Quantifier();

        $lexer->moveNext();
        $qual->parse($scope, $scope, $lexer);

        $this->assertEquals(5, $scope->getMinOccurrences());
        $this->assertEquals(5, $scope->getMaxOccurrences());
    }

    public function testQuantifierSpacesIncluded(): void
    {
        $pattern = '{ 1 , 5 }';
        $lexer = new Kartigex\Lexer($pattern);
        $scope = new Kartigex\Generator\Scope();
        $qual = new Kartigex\Parser\Quantifier();

        $lexer->moveNext();
        $qual->parse($scope, $scope, $lexer);

        $this->assertEquals(1, $scope->getMinOccurrences());
        $this->assertEquals(5, $scope->getMaxOccurrences());
    }

    /**
     * @expectedException \Kartavik\Kartigex\Exception
     * @expectedExceptionMessage Quantifier expects and integer compitable string
     */
    public function testFailedAlphaCharacters(): void
    {
        $pattern = '{ 1 , 5a }';
        $lexer = new Kartigex\Lexer($pattern);
        $scope = new Kartigex\Generator\Scope();
        $qual = new Kartigex\Parser\Quantifier();

        $lexer->moveNext();
        $qual->parse($scope, $scope, $lexer);
    }

    /**
     * @expectedException \Kartavik\Kartigex\Exception
     * @expectedExceptionMessage Quantifier expects and integer compitable string
     */
    public function testFailedMissingMaximumCharacters(): void
    {
        $pattern = '{ 1 ,}';
        $lexer = new Kartigex\Lexer($pattern);
        $scope = new Kartigex\Generator\Scope();
        $qual = new Kartigex\Parser\Quantifier();

        $lexer->moveNext();
        $qual->parse($scope, $scope, $lexer);
    }

    /**
     * @expectedException \Kartavik\Kartigex\Exception
     * @expectedExceptionMessage Quantifier expects and integer compitable string
     */
    public function testFailedMissingMinimumCharacters()
    {
        $pattern = '{,1}';
        $lexer = new Kartigex\Lexer($pattern);
        $scope = new Kartigex\Generator\Scope();
        $qual = new Kartigex\Parser\Quantifier();

        $lexer->moveNext();
        $qual->parse($scope, $scope, $lexer);
    }

    /**
     * @expectedException \Kartavik\Kartigex\Exception
     * @expectedExceptionMessage Closing quantifier token `}` not found
     */
    public function testMissingClosureCharacter(): void
    {
        $pattern = '{1,1';
        $lexer = new Kartigex\Lexer($pattern);
        $scope = new Kartigex\Generator\Scope();
        $qual = new Kartigex\Parser\Quantifier();

        $lexer->moveNext();
        $qual->parse($scope, $scope, $lexer);
    }

    /**
     * @expectedException \Kartavik\Kartigex\Exception
     * @expectedExceptionMessage Nesting Quantifiers is not allowed
     */
    public function testNestingQuantifiers(): void
    {
        $pattern = '{1,1{1,1}';
        $lexer = new Kartigex\Lexer($pattern);
        $scope = new Kartigex\Generator\Scope();
        $qual = new Kartigex\Parser\Quantifier();

        $lexer->moveNext();
        $qual->parse($scope, $scope, $lexer);
    }

    public function testStarQuantifier(): void
    {
        $pattern = 'az*';
        $lexer = new Kartigex\Lexer($pattern);
        $scope = new Kartigex\Generator\Scope();
        $qual = new Kartigex\Parser\Quantifier();

        $lexer->moveNext();
        $lexer->moveNext();
        $lexer->moveNext();

        $qual->parse($scope, $scope, $lexer);

        $this->assertEquals(0, $scope->getMinOccurrences());
        $this->assertEquals(PHP_INT_MAX, $scope->getMaxOccurrences());
    }

    public function testCrossQuantifier(): void
    {
        $pattern = 'az+';
        $lexer = new Kartigex\Lexer($pattern);
        $scope = new Kartigex\Generator\Scope();
        $qual = new Kartigex\Parser\Quantifier();

        $lexer->moveNext();
        $lexer->moveNext();
        $lexer->moveNext();
        $qual->parse($scope, $scope, $lexer);

        $this->assertEquals(1, $scope->getMinOccurrences());
        $this->assertEquals(PHP_INT_MAX, $scope->getMaxOccurrences());
    }

    public function testQuestionQuantifier(): void
    {
        $pattern = 'az?';
        $lexer = new Kartigex\Lexer($pattern);
        $scope = new Kartigex\Generator\Scope();
        $qual = new Kartigex\Parser\Quantifier();

        $lexer->moveNext();
        $lexer->moveNext();
        $lexer->moveNext();
        $qual->parse($scope, $scope, $lexer);

        $this->assertEquals(0, $scope->getMinOccurrences());
        $this->assertEquals(1, $scope->getMaxOccurrences());
    }
}
