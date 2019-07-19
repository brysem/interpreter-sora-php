<?php

namespace Bryse\Sora\Exceptions;

use RuntimeException;
use Throwable;

class InterpreterException extends RuntimeException
{
    /**
     * The code that was run.
     *
     * @var string
     */
    protected $code;

    public function __construct(string $message, $code = null, Throwable $previous = null)
    {
        $this->code = $message;
        $this->message = is_null($code)
            ? 'Error parsing input'
            : 'Unexpected token "'. $code .'"';

        dd([
            'error' => __CLASS__,
            'message' => $this->message,
            'code' => $this->code,
            'stacktrace' => explode(PHP_EOL, $this->getTraceAsString()),
        ]);
    }
}
