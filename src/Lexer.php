<?php

namespace Bryse\Sora;

use Bryse\Sora\Exceptions\SyntaxException;
use Bryse\Sora\Support\Arr;
use Bryse\Sora\Parser\Position;

class Lexer
{
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

    protected $operators = [
        '+' => Token::PLUS,
        '-' => Token::MINUS,
        '/' => Token::DIVIDE,
        '*' => Token::MULTIPLY,
    ];

    protected $parenthesis = [
        '(' => Token::LEFT_PARENTHESIS,
        ')' => Token::RIGHT_PARENTHESIS,
    ];

    protected $reservedKeywords = [];

    /**
     * @param string $code Client string input, e.g. "3 * 5", "12 / 3 * 4", etc
     */
    public function __construct(string $code)
    {
        $this->code = $code;
        $this->position = 0;
        $this->currentToken = $this->code[$this->position] ?? null;

        $this->reservedKeywords = [
            'BEGIN' => new Token('BEGIN', 'BEGIN'),
            'END'   => new Token('END', 'END'),
        ];
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
        throw SyntaxException::throw($currentChar, $this->getPosition());
    }

    /**
     * Handle identifiers and reserved keywords.
     *
     * @return Token
     */
    public function id(): Token
    {
        $result = '';
        $currentChar = $this->getCurrentChar();

        while (! \is_null($currentChar) && $currentChar != ' ' && \ctype_alnum($currentChar)) {
            $result .= $currentChar;
            $this->advance();

            $currentChar = $this->getCurrentChar();
        }

        $token = Arr::get(
            $this->reservedKeywords,
            $result,
            new Token(Token::ID, $result)
        );

        return $token;
    }

    /**
     * Advances $this->position and sets the $this->currentToken variable.
     *
     * @return void
     */
    protected function advance($amount = 1)
    {
        for ($i = 0; $i < $amount; $i++) {
            $this->position += 1;

            if ($this->position > \strlen($this->code) - 1) {
                $this->currentToken = null;

                return;
            }

            $this->currentToken = $this->code[$this->position];
        }
    }

    /**
     * Skips the interpreter input to the next non-whitespace.
     *
     * @return void
     */
    protected function skipWhitespace(): void
    {
        while ($this->getCurrentChar() == ' ' || $this->getCurrentChar() == PHP_EOL) {
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
     * Peeks at the next character in the code one character ahead of the current position.
     *
     * @return string|null
     */
    public function peek(): ?string
    {
        $peekPosition = $this->position + 1;

        if ($peekPosition > \strlen($this->code) - 1) {
            return null;
        }

        return $this->code[$peekPosition];
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

        $currentChar = $this->getCurrentChar();
        while ($currentChar != null) {
            if (! $currentChar = $this->getCurrentChar()) {
                break;
            }

            if ($currentChar == ' ' || $currentChar == PHP_EOL) {
                $this->skipWhitespace();
                continue;
            }

            if (\is_numeric($currentChar)) {
                return new Token(Token::INTEGER, $this->integer());
            }

            if (\ctype_alnum($currentChar)) {
                return $this->id();
            }

            if ($currentChar == ':' && $this->peek() == '=') {
                $this->advance(2);

                return new Token(Token::ASSIGNMENT, ':=');
            }

            if ($currentChar == ';') {
                $this->advance();

                return new Token(Token::SEMICOLON, $currentChar);
            }

            if (\in_array($currentChar, \array_keys($this->operators))) {
                $this->advance();

                return new Token($this->operators[$currentChar], $currentChar);
            }

            if (\in_array($currentChar, \array_keys($this->parenthesis))) {
                $this->advance();

                return new Token($this->parenthesis[$currentChar], $currentChar);
            }

            if ($currentChar == '.') {
                $this->advance();

                return new Token(Token::DOT, $currentChar);
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

    /**
     * Returns the position for the current pointer of the code.
     *
     * @return Position
     */
    public function getPosition(): Position
    {
        return new Position($this->code, $this->position);
    }
}
