<?php

namespace Bryse\Sora\Support;

class Str
{
    public static function dedent(string $string): string
    {
        $string = trim($string);
        $parts = explode(PHP_EOL, $string);
        return implode(PHP_EOL, array_map('trim', $parts));
    }
}
