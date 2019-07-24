<?php

namespace Bryse\Sora\Parser\Nodes;

class Compound extends Node
{
    /**
     * An array of statements.
     *
     * @var Node[]
     */
    protected $children = [];

    /**
     * @param Node[] $children
     */
    public function __construct($children = [])
    {
        $this->children = $children;
    }

    /**
     * Returns an array of statements for this compound.
     *
     * @return Node[]
     */
    public function children(): array
    {
        return $this->children;
    }
}
