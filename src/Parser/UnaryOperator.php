<?php

namespace Bryse\Sora\Parser;

use Bryse\Sora\Token;

class UnaryOperator extends Node
{
    protected $token;
    protected $operator;
    protected $expression;

    public function __construct(Token $operator, Node $expression)
    {
        $this->token = $this->operator = $operator;
        $this->expression = $expression;
    }

    public function expression(): Node
    {
        return $this->expression;
    }
}
