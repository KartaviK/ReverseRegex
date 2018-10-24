<?php
namespace ReverseRegex\Test;

use ReverseRegex\Generator\Scope;
use ReverseRegex\Random\MersenneRandom;
use ReverseRegex\Generator\LiteralScope;

class ScopeTest extends Basic
{
    
    public function testScopeImplementsRepeatInterface()
    {
        $scope = new Scope('scope1');
        $this->assertInstanceOf('ReverseRegex\Generator\RepeatInterface', $scope);
    }
    
    public function testScopeImplementsContextInterface()
    {
        $scope = new Scope('scope1');
        $this->assertInstanceOf('ReverseRegex\Generator\ContextInterface', $scope);
    }
    
    public function testScopeExtendsNode()
    {
        $scope = new Scope('scope1');
        $this->assertInstanceOf('ReverseRegex\Generator\Node', $scope);
    }
    
    public function testScopeImplementsAlternateInterface()
    {
        $scope = new Scope('scope1');
        $this->assertInstanceOf('ReverseRegex\Generator\AlternateInterface', $scope);
    }


    public function testAlternateInterface()
    {
        $scope = new Scope('scope1');
        $this->assertFalse($scope->usingAlternatingStrategy());
        
        $scope->useAlternatingStrategy(true);
        $this->assertTrue($scope->usingAlternatingStrategy());
    }
    
        
    public function testRepeatInterface()
    {
        $scope  = new Scope('scope1');
        
        $scope->setMaxOccurrences(10);
        $scope->setMinOccurrences(5);

        $this->assertEquals(10, $scope->getMaxOccurrences());
        $this->assertEquals(5, $scope->getMinOccurrences());
        $this->assertEquals(5, $scope->getOccurrenceRange());
    }
    
    
    public function testAttachChild()
    {
        $scope  = new Scope('scope1');
        $scope2  = new Scope('scope2');
       
        $scope->attach($scope2)->rewind();
        $this->assertEquals($scope2, $scope->current());
    }
    
    
    public function testRepeatQuota()
    {
        $gen = new MersenneRandom(703);
        
        $scope = new Scope('scope1');
        $scope->setMinOccurrences(1);
        $scope->setMaxOccurrences(6);
        
        $this->assertEquals(3, $scope->calculateRepeatQuota($gen));
    }
    
    /**
      *  @expectedException \ReverseRegex\Exception
      *  @expectedExceptionMessage No child scopes to call must be atleast 1
      */
    public function testGenerateErrorNotChildren()
    {
        $gen = new MersenneRandom(700);
        
        $scope = new Scope('scope1');
        $scope->setMinOccurrences(1);
        $scope->setMaxOccurrences(6);
        
        $result = '';
        
        $scope->generate($result, $gen);
    }
    
    
    public function testGenerate()
    {
        
        $gen = new MersenneRandom(700);
        $result = '';
        
        $scope = new Scope('scope1');
        $scope->setMinOccurrences(6);
        $scope->setMaxOccurrences(6);
        
        $child = $this->getMockBuilder('ReverseRegex\Generator\Scope')->setMethods(['generate'])->getMock();
        
        $child->expects($this->exactly(6))
            ->method('generate')
            ->with($this->isType('string'), $this->equalTo($gen))
            ->will($this->returnCallback(function (&$sResult) {
                return $sResult .= 'a';
            }));
        
        $scope->attach($child);
        
        $result = $scope->generate($result, $gen);
        
        $this->assertEquals('aaaaaa', $result);
    }
    
    public function testGetNode()
    {
        $scope = new Scope('scope1');
        
     
        
        for ($i = 1; $i <= 6; $i++) {
            $scope->attach(new Scope('label_'.$i));
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
    
    
    public function testGenerateWithAlternatingStrategy()
    {
        $scope  = new Scope('scope1');
        $gen    = new MersenneRandom(700);
        $result = '';
        
        $scope->setMinOccurrences(7);
        $scope->setMaxOccurrences(7);
        
        for ($i = 1; $i <= 6; $i++) {
            $lit = new LiteralScope('label_'.$i);
            $lit->addLiteral($i);
            $scope->attach($lit);
            $lit = null;
        }
        
        $scope->useAlternatingStrategy();
        $scope->generate($result, $gen);
        $this->assertRegExp('/[1-6]{7}/', $result);
    }
}
/* End of File */
