<?php

namespace Bryse\Sora;

class Token
{
    const INTEGER = 'INTEGER';
    const PLUS = 'PLUS';
    const MINUS = 'MINUS';
    const MULTIPLY = 'MULTIPLY';
    const DIVIDE = 'DIVIDE';
    const WHITESPACE = 'WHITESPACE';
    const LEFT_PARENTHESIS = 'LEFT_PARENTHESIS';
    const RIGHT_PARENTHESIS = 'RIGHT_PARENTHESIS';
    const ASSIGNMENT = ':=';
    const ID = 'ID';
    const BEGIN = 'BEGIN';
    const END = 'END';
    const SEMICOLON = ';';
    const DOT = '.';
    const EOF = 'EOF';

    protected $type;
    protected $value;

    public function __construct(string $type, $value)
    {
        $this->type = $type;
        $this->value = $this->cast($this->type, $value);
    }

    public function type()
    {
        return $this->type;
    }

    /**
     * Returns the value of the token.
     * Provide a value to update the current value and return it.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function value($value = null)
    {
        if (! \is_null($value)) {
            $this->value = $value;
        }

        return $this->value;
    }

    protected function cast(string $type, $value)
    {
        switch ($type) {
            case self::INTEGER:
                return (int) $value;
            default:
                return (string) $value;
        }
    }

    public function __toString()
    {
        return "Token({$this->type}, {$this->value})";
    }
}
