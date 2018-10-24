<?php

namespace ReverseRegex\Generator;

use PHPStats\Generator\GeneratorInterface;
use ReverseRegex;

/**
 * Class Scope
 * @package ReverseRegex\Generator
 *
 * Base class for Scopes
 *
 * @author Lewis Dyer <getintouch@icomefromthenet.com>
 * @since 0.0.1
 */
class Scope extends Node implements ContextInterface, RepeatInterface, AlternateInterface
{
    public const REPEAT_MIN_INDEX = 'repeat_min';
    public const REPEAT_MAX_INDEX = 'repeat_max';
    public const USE_ALTERNATING_INDEX = 'use_alternating';

    public function __construct(string $label = 'node')
    {
        parent::__construct($label);

        $this[self::USE_ALTERNATING_INDEX] = false;

        $this->setMinOccurrences(1);
        $this->setMaxOccurrences(1);
    }

    /**
     * {@inheritdoc}
     *
     * @throws ReverseRegex\Exception
     */
    public function generate(string &$result, GeneratorInterface $generator): void
    {
        if ($this->count() === 0) {
            throw new ReverseRegex\Exception('No child scopes to call must be at least 1');
        }

        $repeat_x = $this->calculateRepeatQuota($generator);

        # rewind the current item
        $this->rewind();
        while ($repeat_x > 0) {
            if ($this->usingAlternatingStrategy()) {
                $randomIndex = \round($generator->generate(1, ($this->count())));
                $this->get($randomIndex)->generate($result, $generator);
            } else {
                foreach ($this as $current) {
                    $current->generate($result, $generator);
                }
            }

            $repeat_x = $repeat_x - 1;
        }
    }

    /**
     * Fetch a node given an `one-based index`
     *
     * @param int $index
     *
     * @return Scope|null
     */
    public function get(int $index): ?Scope
    {
        if ($index > $this->count() || $index <= 0) {
            return null;
        }

        $this->rewind();
        while (($index - 1) > 0) {
            $this->next();
            $index = $index - 1;
        }

        return $this->current();
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxOccurrences(): int
    {
        return $this[static::REPEAT_MAX_INDEX];
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxOccurrences(int $number): void
    {
        $this[static::REPEAT_MAX_INDEX] = $number;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinOccurrences(): int
    {
        return $this[static::REPEAT_MIN_INDEX];
    }

    /**
     * {@inheritdoc}
     */
    public function setMinOccurrences(int $num): void
    {
        $this[static::REPEAT_MIN_INDEX] = $num;
    }

    /**
     * {@inheritdoc}
     */
    public function getOccurrenceRange(): int
    {
        return $this->getMaxOccurrences() - $this->getMinOccurrences();
    }

    /**
     * Calculate a random number of repeats given the current min-max range
     *
     * @param GeneratorInterface $generator
     *
     * @return int
     */
    public function calculateRepeatQuota(GeneratorInterface $generator): int
    {
        $repeatX = $this->getMinOccurrences();

        if ($this->getOccurrenceRange() > 0) {
            $repeatX = (int)\round($generator->generate($this->getMinOccurrences(), $this->getMaxOccurrences()));
        }

        return $repeatX;
    }

    /**
     * {@inheritdoc}
     */
    public function useAlternatingStrategy(): void
    {
        $this[static::USE_ALTERNATING_INDEX] = true;
    }

    /**
     * {@inheritdoc}
     */
    public function usingAlternatingStrategy(): bool
    {
        return $this[static::USE_ALTERNATING_INDEX];
    }
}
