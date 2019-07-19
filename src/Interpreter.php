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
        $this->currentToken = null;
    }

    public static function run(string $code)
    {
        $interpreter = new Interpreter($code);
        $result = $interpreter->expression();

        return $result;
    }

    public function expression()
    {
        // expression -> INTEGER PLUS INTEGER
        // set current token to the first token taken from the input
        $this->currentToken = $this->getNextToken();

        $left = null;
        $operator = null;
        $right = null;
        while($this->currentToken->type() != Token::EOF) {
            if ($this->currentToken->type() == Token::WHITESPACE) {
                $this->eat(Token::WHITESPACE);
                continue;
            }

            if ($this->currentToken->type() == Token::INTEGER && ! $operator) {
                $left = $left
                    ? new Token(Token::INTEGER, (string) $left->value() . (string) $this->currentToken->value())
                    : $this->currentToken;

                $this->eat(Token::INTEGER);
                continue;
            }

            if ($this->currentToken->type() != Token::INTEGER && $left) {
                $operator = $this->currentToken;
                $this->eat([Token::PLUS, Token::MINUS, Token::MULTIPLY, Token::DIVIDE]);
                continue;
            }

            if ($this->currentToken->type() == Token::INTEGER && $operator) {
                $right = $right
                    ? new Token(Token::INTEGER, (string) $right->value() . (string) $this->currentToken->value())
                    : $this->currentToken;

                $this->eat(Token::INTEGER);
                continue;
            }
        }

        // after the above call the self.current_token is set to
        // EOF token

        // at this point INTEGER PLUS INTEGER sequence of tokens
        // has been successfully found and the method can just
        // return the result of adding two integers, thus
        // effectively interpreting client input
        switch($operator->type()) {
            case Token::PLUS:
                return $left->value() + $right->value();
            case Token::MINUS:
                return $left->value() - $right->value();
            case Token::MULTIPLY:
                return $left->value() * $right->value();
            case Token::DIVIDE:
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
        // If position is past the end of the last char in the code,
        // we will then return the EOF token because there is
        // no more input left to convert into tokens.
        if ($this->position > strlen($this->code) - 1) {
            return new Token(Token::EOF, null);
        }

        // get a character at the position self.pos and decide
        // what token to create based on the single character
        $currentChar = substr($this->code, $this->position, 1);

        // if the character is an empty whitespace we should skip
        if ($currentChar == " ") {
            $token = new Token(Token::WHITESPACE, $currentChar);
            $this->position++;

            return $token;
        }

        // if the character is a digit then convert it to
        // integer, create an INTEGER token, increment self.pos
        // index to point to the next character after the digit,
        // and return the INTEGER token
        if (is_numeric($currentChar)) {
            $token = new Token(Token::INTEGER, $currentChar);
            $this->position++;

            return $token;
        }

        if ($currentChar == '+') {
            $token = new Token(Token::PLUS, $currentChar);
            $this->position++;

            return $token;
        }

        if ($currentChar == '-') {
            $token = new Token(Token::MINUS, $currentChar);
            $this->position++;

            return $token;
        }

        if ($currentChar == '*') {
            $token = new Token(Token::MULTIPLY, $currentChar);
            $this->position++;

            return $token;
        }

        if ($currentChar == '/') {
            $token = new Token(Token::DIVIDE, $currentChar);
            $this->position++;

            return $token;
        }

        $this->error($currentChar);
    }

    /**
     * compare the current token type with the passed token
     * type and if they match then "eat" the current token
     * and assign the next token to the self.current_token,
     * otherwise raise an exception.
     *
     * @param string|array $tokenType
     * @return void
     */
    public function eat($tokenType)
    {
        $tokenType = ! is_array($tokenType) ? [$tokenType] : $tokenType;

        if (! in_array($this->currentToken->type(), $tokenType)) {
            $this->error();
        }

        $this->currentToken = $this->getNextToken();
    }
}
