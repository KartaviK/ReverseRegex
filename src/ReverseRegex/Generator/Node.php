<?php

namespace ReverseRegex\Generator;

/**
 * Class Node
 * @package ReverseRegex\Generator
 *
 * Base to all Generator Scopes
 */
class Node implements \ArrayAccess, \Countable, \Iterator
{
    /** @var string name of the node */
    protected $label;

    /** @var \ArrayObject container for node metadata */
    protected $attributes;

    /** @var \SplObjectStorage container for node relationships */
    protected $links;

    public function __construct($label = 'node')
    {
        $this->attributes = new \ArrayObject();
        $this->links = new \SplObjectStorage();

        $this->setLabel($label);
    }

    /**
     * Fetch the nodes label
     *
     * @return string The nodes label
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Sets the node label
     *
     * @param string $label the nodes label
     */
    public function setLabel($label): void
    {
        if (!(is_scalar($label) || is_null($label))) {
            return;
        }

        $this->label = $label;
    }

    /**
     * Attach a node
     *
     * @param Node $node the node to attach
     *
     * @return Node
     */
    public function &attach(Node $node): Node
    {
        $this->links->attach($node);

        return $this;
    }

    /**
     * Detach a node
     *
     * @param Node $node the node to remove
     *
     * @return Node
     */
    public function &detach(Node $node): Node
    {
        foreach ($this->links as $linkNode) {
            if ($linkNode == $node) {
                $this->links->detach($node);
            }
        }

        return $this;
    }

    /**
     * Search for node in its relations
     *
     * @param Node $node the node to search for
     *
     * @return boolean True if found
     */
    public function contains(Node $node): bool
    {
        foreach ($this->links as $linked_node) {
            if ($linked_node == $node) {
                return true;
            }
        }

        return false;
    }

    /**
     * Apply a closure to all relations
     *
     * @param \Closure $callback
     */
    public function walk(\Closure $callback): void
    {
        foreach ($this->links as $node) {
            call_user_func($callback, $node);
        }
    }

    public function count(): int
    {
        return count($this->links);
    }

    public function current()
    {
        return $this->links->current();
    }

    public function key()
    {
        return $this->links->key();
    }

    public function next()
    {
        $this->links->next();
    }

    public function rewind()
    {
        $this->links->rewind();
    }

    public function valid()
    {
        return $this->links->valid();
    }

    public function offsetGet($key)
    {
        return $this->attributes->offsetGet($key);
    }

    public function offsetSet($key, $value): void
    {
        $this->attributes->offsetSet($key, $value);
    }

    public function offsetExists($key): bool
    {
        return $this->attributes->offsetExists($key);
    }

    public function offsetUnset($key): void
    {
        $this->attributes->offsetUnset($key);
    }
}
