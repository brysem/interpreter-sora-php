<?php

namespace Bryse\Sora\Exceptions;

use RuntimeException;
use Throwable;

class InterpreterException extends RuntimeException
{
    public function __construct(string $message = null, $code = null, Throwable $previous = null)
    {
        $this->message = $message ?? 'There was an expected error.';
        $this->code = $code;
        $this->previous = $previous;
    }
}
