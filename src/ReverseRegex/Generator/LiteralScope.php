<?php

namespace ReverseRegex\Generator;

use ReverseRegex\Exception;
use PHPStats\Generator\GeneratorInterface;
use ReverseRegex\ArrayCollection;

/**
 * Class LiteralScope
 * @package ReverseRegex\Generator
 *
 * Scope for Literal Values
 */
class LiteralScope extends Scope
{
    /**@var ArrayCollection */
    protected $literals;

    public function __construct(string $label = 'label')
    {
        parent::__construct($label);

        $this->literals = new ArrayCollection();
    }

    /**
     * Adds a literal value to internal collection
     *
     * @param mixed $literal
     */
    public function addLiteral($literal): void
    {
        $this->literals->add($literal);
    }

    /**
     * Sets a value on the internal collection using a key
     *
     * @param string $hex a hexidecimal number
     * @param string $literal the literal to store
     */
    public function setLiteral(string $hex, string $literal): void
    {
        $this->literals->set($hex, $literal);
    }

    /**
     * Return the literal ArrayCollection
     *
     * @return ArrayCollection
     */
    public function getLiterals(): ArrayCollection
    {
        return $this->literals;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function generate(string &$result, GeneratorInterface $generator): void
    {
        if ($this->literals->count() === 0) {
            throw new Exception('There are no literals to choose from');
        }

        $repeat_x = $this->calculateRepeatQuota($generator);

        while ($repeat_x > 0) {
            $randomIndex = 0;

            if ($this->literals->count() > 1) {
                $randomIndex = \round($generator->generate(1, ($this->literals->count())));
            }

            $result .= $this->literals->getAt($randomIndex);

            --$repeat_x;
        }
    }
}
