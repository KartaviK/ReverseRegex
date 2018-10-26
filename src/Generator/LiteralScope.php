<?php

namespace Kartavik\Kartigex\Generator;

use Kartavik\Kartigex;

/**
 * Class LiteralScope
 * @package Kartavik\Kartigex\Generator
 *
 * Scope for Literal Values
 *
 * @author Lewis Dyer <getintouch@icomefromthenet.com>
 * @since 0.0.1
 * @author Roman <KartaviK> Varkuta <roman.varkuta@gmail.com>
 */
class LiteralScope extends Scope
{
    /**@var \ArrayObject */
    protected $literals;

    public function __construct(\ArrayObject $collection = null)
    {
        $this->literals = $collection ?? new \ArrayObject();

        parent::__construct();
    }

    /**
     * Adds a literal value to internal collection
     *
     * @param mixed $literal
     */
    public function addLiteral($literal): void
    {
        $this->literals->append($literal);
    }

    /**
     * Sets a value on the internal collection using a key
     *
     * @param string $hex a hexidecimal number
     * @param string $literal the literal to store
     */
    public function setLiteral(string $hex, string $literal): void
    {
        $this->literals->offsetSet($hex, $literal);
    }

    /**
     * Return the literal ArrayCollection
     *
     * @return \ArrayObject
     */
    public function getLiterals(): \ArrayObject
    {
        return $this->literals;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Kartigex\Exception
     */
    public function generate(string &$result, Kartigex\Contract\GeneratorInterface $generator): void
    {
        if (!$this->literals->count()) {
            throw new Kartigex\Exception('There are no literals to choose from');
        }

        $repeatQuota = $this->calculateRepeatQuota($generator);

        while ($repeatQuota > 0) {
            $randomIndex = 0;

            if ($this->literals->count() > 1) {
                $randomIndex = $generator->generate(1, $this->literals->count());
            }

            $result .= $this->literals->offsetGet($randomIndex);

            --$repeatQuota;
        }
    }
}
