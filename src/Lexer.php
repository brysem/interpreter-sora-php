<?php

namespace Bryse\Sora;

use Bryse\Sora\Exceptions\SyntaxException;

class Lexer {
    /**
     * The source to be analyzed.
     *
     * @var string
     */
    protected $code;

    /**
     * The index of the current position inside $this->code for the lexer.
     *
     * @var int
     */
    protected $position = 0;

    /**
     * The current token.
     *
     * @var Token
     */
    protected $currentToken = null;

    /**
     * @param string $code Client string input, e.g. "3 * 5", "12 / 3 * 4", etc
     */
    public function __construct(string $code) {
        $this->code = $code;
        $this->position = 0;
        $this->currentToken = $this->code[$this->position] ?? null;
    }

    /**
     * Throws an error and terminates code execution.
     *
     * @param string $currentChar
     *
     * @throws InterpreterException
     *
     * @return void
     */
    public function error($currentChar = null): void
    {
        throw SyntaxException::throw($currentChar, $this->getLineNumber(), $this->position);
    }

    /**
     * Advances $this->position and sets the $this->currentToken variable.
     *
     * @return void
     */
    protected function advance()
    {
        $this->position += 1;

        if ($this->position > \strlen($this->code) - 1) {
            $this->currentToken = null;

            return;
        }

        $this->currentToken = $this->code[$this->position];
    }

    /**
     * Skips the interpreter input to the next non-whitespace.
     *
     * @return void
     */
    protected function skipWhitespace(): void
    {
        while ($this->getCurrentChar() == ' ') {
            $this->advance();
        }
    }



    /**
     * Return a (multidigit) integer consumed from the input.
     *
     * @return int
     */
    protected function integer(): int
    {
        $integer = '';

        while (\is_numeric($this->getCurrentChar())) {
            $integer .= $this->getCurrentChar();
            $this->advance();
        }

        return (int) $integer;
    }

    /**
     * Lexical analyzer (also known as scanner or tokenizer)
     * This method is responsible for breaking a sentence
     * apart into tokens. One token at a time.
     *
     * @return Token
     */
    public function getNextToken(): Token
    {
        static $i = 0;
        $i++;

        $operators = [
            '+' => Token::PLUS,
            '-' => Token::MINUS,
            '/' => Token::DIVIDE,
            '*' => Token::MULTIPLY,
        ];

        $currentChar = $this->getCurrentChar();
        while ($currentChar != null) {
            $currentChar = $this->getCurrentChar();
            // dump(['i' => $i, 'pos' => $this->position, 'char' => $currentChar]);

            if ($currentChar == ' ') {
                $this->skipWhitespace();
                continue;
            }

            if (\is_numeric($currentChar)) {
                return new Token(Token::INTEGER, $this->integer());
            }

            if (\in_array($currentChar, \array_keys($operators))) {
                $this->advance();

                return new Token($operators[$currentChar], $currentChar);
            }

            $this->error($currentChar);
        }

        return new Token(Token::EOF, null);
    }

    /**
     * Retrieves the character for the current position of the interpreter.
     *
     * @return string|null
     */
    public function getCurrentChar(): ?string
    {
        if ($this->position > \strlen($this->code) - 1) {
            return null;
        }

        return $this->code[$this->position];
    }

    protected function getLineNumber()
    {
        $position = $this->position;
        $currentLine = 1;
        $lines = explode(PHP_EOL, $this->code);

        foreach ($lines as $line) {
            $length = strlen($line);
            if ($position > $length) {
                $position -= $length;
                $currentLine++;
            }

            continue;
        }

        return $currentLine;
    }
}
