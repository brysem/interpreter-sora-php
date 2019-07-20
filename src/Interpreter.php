<?php

namespace Bryse\Sora;

use Bryse\Sora\Exceptions\InterpreterException;

class Interpreter
{
    protected $code;
    protected $position = 0;
    protected $currentToken = null;

    public function __construct($code)
    {
        $this->code = $code;
        $this->position = 0;
        $this->currentToken = $this->code[0];
    }

    public static function run(string $code)
    {
        $interpreter = new self($code);
        // dump(['code' => $code]);
        $result = $interpreter->expression();

        return $result;
    }

    protected function advance()
    {
        $this->position += 1;

        if ($this->position > strlen($this->code) - 1) {
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
        while($this->getCurrentChar() == ' ') {
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

        while(is_numeric($this->getCurrentChar())) {
            $integer .= $this->getCurrentChar();
            $this->advance();
        }

        return (int) $integer;
    }

    public function expression()
    {
        // expression -> INTEGER PLUS INTEGER
        // set current token to the first token taken from the input
        $this->currentToken = $this->getNextToken();

        // We expect the current token to be an integer
        $left = $this->currentToken;
        // dump(['left' => $this->currentToken]);
        $this->eat(Token::INTEGER);

        // We expect the current token to be either a '+', '-', '*', or '/'
        $operator = $this->currentToken;
        // dump(['operator' => $this->currentToken]);
        $this->eat([Token::PLUS, Token::MINUS, Token::MULTIPLY, Token::DIVIDE]);

        // We expect the current token to be an integer
        $right = $this->currentToken;
        // dump(['right' => $this->currentToken]);
        $this->eat(Token::INTEGER);

        // After the above call the self.current_token is set to
        // EOF token

        // At this point the INTEGER PLUS INTEGER sequence of tokens
        // has been successfully found and the method can just
        // return the result of adding two integers, thus
        // effectively interpreting client input
        switch ($operator->type()) {
            case Token::PLUS:
                return $left->value() + $right->value();
            case Token::MINUS:
                return $left->value() - $right->value();
            case Token::MULTIPLY:
                return $left->value() * $right->value();
            case Token::DIVIDE:
                if (in_array(0, [$left->value(), $right->value()])) {
                    $this->error('Division by zero');
                }

                return $left->value() / $right->value();
            default:
                return $this->error();
        }
    }

    public function error($currentChar = null)
    {
        throw new InterpreterException($this->code, $currentChar);
    }

    /**
     * Lexical analyzer (also known as scanner or tokenizer)
     * This method is responsible for breaking a sentence
     * apart into tokens. One token at a time.
     *
     * @return void
     */
    public function getNextToken()
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
        while($currentChar != null) {
            $currentChar = $this->getCurrentChar();
            // dump(['i' => $i, 'pos' => $this->position, 'char' => $currentChar]);

            if ($currentChar == ' ') {
                $this->skipWhitespace();
                continue;
            }

            if (is_numeric($currentChar)) {
                return new Token(Token::INTEGER, $this->integer());
            }

            if (in_array($currentChar, array_keys($operators))) {
                $this->advance();
                return new Token($operators[$currentChar], $currentChar);
            }

            $this->error($currentChar);
        }

        return new Token(Token::EOF, null);
    }

    /**
     * compare the current token type with the passed token
     * type and if they match then "eat" the current token
     * and assign the next token to the self.current_token,
     * otherwise raise an exception.
     *
     * @param string|array $tokenType
     *
     * @return void
     */
    public function eat($tokenType)
    {
        $tokenType = ! \is_array($tokenType) ? [$tokenType] : $tokenType;

        if (! \in_array($this->currentToken->type(), $tokenType)) {
            $this->error();
        }

        $this->currentToken = $this->getNextToken();
    }

    /**
     * Retrieves the character for the current position of the interpreter.
     *
     * @return string|null
     */
    protected function getCurrentChar(): ?string
    {
        if ($this->position > strlen($this->code) - 1) {
            return null;
        }

        return $this->code[$this->position];
    }
}
