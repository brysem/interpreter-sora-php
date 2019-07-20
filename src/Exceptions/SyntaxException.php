<?php

namespace Bryse\Sora\Exceptions;

use RuntimeException;
use Throwable;

class SyntaxException extends RuntimeException
{
    /**
     * The code that was run.
     *
     * @var string
     */
    protected $code;

    public function __construct(string $message, int $code = 0, Throwable $previous = null)
    {
        $this->message = $message;
        $this->code = $code;
        $this->previous = $previous;
    }

    public static function throw(string $currentChar = null, int $line = 1, int $position = 0)
    {
        $message = is_null($currentChar)
            ? 'Error parsing input'
            : sprintf("Parse error: syntax error, unexpected '%s' in sora shell code on line %s:%s", $currentChar, $line, $position);

        return new static($message);
    }
}
