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

    public function value($value = null)
    {
        if (! is_null($value)) {
            $this->value = $value;
        }

        return $this->value;
    }

    protected function cast(string $type, $value)
    {
        switch($type) {
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