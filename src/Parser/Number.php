<?php

namespace Bryse\Sora\Parser;

use Bryse\Sora\Token;

class Number extends Node
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