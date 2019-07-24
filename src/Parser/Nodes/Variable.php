<?php

namespace Bryse\Sora\Parser\Nodes;

use Bryse\Sora\Token;

class Variable extends Node
{
    /**
     * The token instance.
     *
     * @var Token
     */
    protected $token;

    protected $value;

    public function __construct(Token $token)
    {
        $this->token = $token;
        $this->value = $this->token->value();
    }
}
