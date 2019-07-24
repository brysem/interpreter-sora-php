<?php

namespace Bryse\Sora\Parser;

class Position
{
    /**
     * The code that is being analyzed.
     *
     * @var string[]
     */
    protected $code;

    /**
     * The length per line of code that is being analyzed.
     *
     * @var int[]
     */
    protected $lengths;

    protected $position;

    protected $cache = [];

    /**
     * @param string $code
     * @param integer $position
     */
    public function __construct(string $code, int $position)
    {
        $this->code = explode(PHP_EOL, $code);
        $this->position = $position;
    }

    /**
     * Returns the absolute position
     */
    public function absolute(): int
    {
        return $this->position;
    }

    /**
     * Returns the absolute position
     */
    public function relative(): int
    {
        $lineNumber = $this->lineNumber();

        $remainingCharacters = array_sum($this->code);

        foreach(range(1, $lineNumber) as $i) {
            if ($remainingCharacters - $this->lengths[$i] < 0) {
                return $remainingCharacters;
            }

            $remainingCharacters -= $this->lengths[$i];
        }

        return $this->position;
    }

    public function lineNumber()
    {
        if (isset($this->cache[$this->position])) {
            return $this->cache[$this->position];
        }

        $position = $this->position;
        $currentLine = 0;
        $lines = $this->code;

        foreach ($lines as $line) {
            $length = \strlen($line);
            if ($position > $length) {
                $position -= $length;
                $currentLine++;
            }

            $this->lengths[$currentLine] = $length;

            continue;
        }

        $this->cache[$this->position] = $currentLine;

        return $currentLine;
    }

    public function line(int $number = null)
    {
        $number = is_null($number) ? $this->lineNumber() : $number;
        $lines = $this->code;

        return $lines[$number - 1] ?? '';
    }
}
