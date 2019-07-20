<?php

namespace Bryse\Sora\Parser;

use Bryse\Sora\Token;

abstract class Node
{
    /**
     * Returns the token defining the node.
     *
     * @return Token
     */
    public function token(): Token
    {
        return $this->token;
    }
    /**
     * Returns the node's value.
     *
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }
}
