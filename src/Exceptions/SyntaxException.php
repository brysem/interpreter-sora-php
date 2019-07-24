<?php

namespace Bryse\Sora\Exceptions;

use RuntimeException;
use Throwable;
use Bryse\Sora\Parser\Position;

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

    public static function throw(string $currentChar = null, Position $position)
    {
        if (is_null($currentChar)) {
            new static('Error parsing input');
        }

        $message = [
            'Parse error: syntax error;',
            '  %s',
            "unexpected '%s' in sora code on line %s:%s"
        ];

        $currentLine = $position->line();
        $line = $position->lineNumber();
        $relativePosition = $position->relative();

        $currentLine = trim($currentLine);
        $currentChar = self::normalizeChar($currentChar);

        return new static(sprintf(implode(PHP_EOL, $message), $currentLine, $currentChar, $line, $relativePosition));
    }

    protected static function normalizeChar(string $char = null)
    {
        return str_replace("\n", '\n', $char);
    }
}
