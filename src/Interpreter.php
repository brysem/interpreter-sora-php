<?php

namespace Bryse\Sora;

class Interpreter
{
    protected $code;

    public function __construct()
    {
        //
    }

    public static function run(string $code)
    {
        $interpreter = new static($code);
        $code = $interpreter->sanitize($code);
        $interpreter->execute($code);
        dd($code);
        return new static($code);
    }

    public function sanitize(string $code)
    {
		// Unify new-lines.
		$code = \preg_replace('#(\r\n)#u', "\n", $code);
		// Ensure newline at the end (parser needs this to be able to correctly
		// parse comments in one line source codes.)
        return rtrim($code) . "\n";
    }

    public function execute(string $code)
    {
        dd($code);
    }
}
