<?php

namespace Bryse\Sora\Parser;

class Compound extends Node
{
    /**
     * An array of statements.
     *
     * @var array
     */
    protected $children = [];

    public function __construct()
    {
        $this->children = [];
    }
}
