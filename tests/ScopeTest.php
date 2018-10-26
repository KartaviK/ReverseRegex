<?php

namespace Kartavik\Kartigex\Test;

use Kartavik\Kartigex;
use PHPUnit\Framework\TestCase;

/**
 * Class ScopeTest
 * @package Kartavik\Kartigex\Test
 * @internal
 */
class ScopeTest extends TestCase
{
    public function testScopeImplementsRepeatInterface(): void
    {
        $scope = new Kartigex\Generator\Scope('scope1');
        $this->assertInstanceOf(Kartigex\Generator\RepeatInterface::class, $scope);
    }

    public function testScopeImplementsContextInterface(): void
    {
        $scope = new Kartigex\Generator\Scope('scope1');
        $this->assertInstanceOf(Kartigex\Generator\ContextInterface::class, $scope);
    }

    public function testScopeExtendsNode(): void
    {
        $scope = new Kartigex\Generator\Scope('scope1');
        $this->assertInstanceOf(Kartigex\Generator\Node::class, $scope);
    }

    public function testScopeImplementsAlternateInterface(): void
    {
        $scope = new Kartigex\Generator\Scope('scope1');
        $this->assertInstanceOf(Kartigex\Generator\AlternateInterface::class, $scope);
    }

    public function testAlternateInterface(): void
    {
        $scope = new Kartigex\Generator\Scope('scope1');
        $this->assertFalse($scope->usingAlternatingStrategy());

        $scope->useAlternatingStrategy();
        $this->assertTrue($scope->usingAlternatingStrategy());
    }

    public function testRepeatInterface(): void
    {
        $scope = new Kartigex\Generator\Scope('scope1');

        $scope->setMaxOccurrences(10);
        $scope->setMinOccurrences(5);

        $this->assertEquals(10, $scope->getMaxOccurrences());
        $this->assertEquals(5, $scope->getMinOccurrences());
        $this->assertEquals(5, $scope->getOccurrenceRange());
    }

    public function testAttachChild(): void
    {
        $first = new Kartigex\Generator\Scope('scope1');
        $second = new Kartigex\Generator\Scope('scope2');

        $first->attach($second)->rewind();
        $this->assertEquals($second, $first->current());
    }

    public function testRepeatQuota(): void
    {
        $gen = new Kartigex\Random\MersenneRandom(703);

        $scope = new Kartigex\Generator\Scope('scope1');
        $scope->setMinOccurrences(1);
        $scope->setMaxOccurrences(6);

        $this->assertEquals(3, $scope->calculateRepeatQuota($gen));
    }

    /**
     * @expectedException \Kartavik\Kartigex\Exception
     * @expectedExceptionMessage No child scopes to call must be atleast 1
     */
    public function testGenerateErrorNotChildren(): void
    {
        $gen = new Kartigex\Random\MersenneRandom(700);

        $scope = new Kartigex\Generator\Scope('scope1');
        $scope->setMinOccurrences(1);
        $scope->setMaxOccurrences(6);

        $result = '';

        $scope->generate($result, $gen);
    }

    public function testGenerate(): void
    {
        $gen = new Kartigex\Random\MersenneRandom(700);
        $result = '';

        $scope = new Kartigex\Generator\Scope('scope1');
        $scope->setMinOccurrences(6);
        $scope->setMaxOccurrences(6);

        $child = $this->getMockBuilder(Kartigex\Generator\Scope::class)->setMethods(['generate'])->getMock();

        $child->expects($this->exactly(6))
            ->method('generate')
            ->with($this->isType('string'), $this->equalTo($gen))
            ->will($this->returnCallback(function (&$sResult) {
                return $sResult .= 'a';
            }));

        $scope->attach($child);

        $scope->generate($result, $gen);

        $this->assertEquals('aaaaaa', $result);
    }

    public function testGetNode(): void
    {
        $scope = new Kartigex\Generator\Scope('scope1');

        for ($i = 1; $i <= 6; $i++) {
            $scope->attach(new Kartigex\Generator\Scope('label_' . $i));
        }

        $other_scope = $scope->get(6);
        $this->assertInstanceOf('ReverseRegex\Generator\Scope', $other_scope);
        $this->assertEquals('label_6', $other_scope->getLabel());

        $other_scope = $scope->get(1);
        $this->assertInstanceOf('ReverseRegex\Generator\Scope', $other_scope);
        $this->assertEquals('label_1', $other_scope->getLabel());


        $other_scope = $scope->get(3);
        $this->assertInstanceOf('ReverseRegex\Generator\Scope', $other_scope);
        $this->assertEquals('label_3', $other_scope->getLabel());

        $other_scope = $scope->get(0);
        $this->assertEquals(null, $other_scope);
    }

    public function testGenerateWithAlternatingStrategy(): void
    {
        $scope = new Kartigex\Generator\Scope('scope1');
        $gen = new Kartigex\Random\MersenneRandom(700);
        $result = '';

        $scope->setMinOccurrences(7);
        $scope->setMaxOccurrences(7);

        for ($i = 1; $i <= 6; $i++) {
            $lit = new Kartigex\Generator\LiteralScope('label_' . $i);
            $lit->addLiteral($i);
            $scope->attach($lit);
            $lit = null;
        }

        $scope->useAlternatingStrategy();
        $scope->generate($result, $gen);
        $this->assertRegExp('/[1-6]{7}/', $result);
    }
}
