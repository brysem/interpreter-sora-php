<?php

namespace Bryse\Sora\Parser\Nodes;

use Bryse\Sora\Token;

class BinaryOperator extends Node
{
    protected $left;
    protected $token;
    protected $operator;
    protected $right;

    public function __construct($left, Token $operator, $right)
    {
        $this->left = $left;
        $this->token = $this->operator = $operator;
        $this->right = $right;
    }

    public function left(): Node
    {
        return $this->left;
    }

    public function right(): Node
    {
        return $this->right;
    }

    public function operator(): Token
    {
        return $this->token();
    }
}
