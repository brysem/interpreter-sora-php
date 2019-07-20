<?php

namespace Bryse\Sora;

use Bryse\Sora\Exceptions\InterpreterException;

class Interpreter
{
    /**
     * The lexer instance for handling lexical analysis of the code.
     *
     * @var Lexer
     */
    protected $lexer;

    protected $currentToken = null;

    public function __construct(Lexer $lexer)
    {
        $this->lexer = $lexer;
        $this->currentToken = $this->lexer->getNextToken();
    }

    public static function run(string $code)
    {
        $lexer = new Lexer($code);
        $interpreter = new self($lexer);

        $result = $interpreter->expression();

        return $result;
    }

    public function error()
    {
        throw new InterpreterException();
    }

    /**
     * Compare the current token type with the passed token
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

        $this->currentToken = $this->lexer->getNextToken();
    }

    /**
     * Returns an INTEGER token value.
     * factor : INTEGER
     *
     * @return int
     */
    public function factor(): int
    {
        $token = $this->currentToken;
        $this->eat(Token::INTEGER);

        return (int) $token->value();
    }

    /**
     * Returns an INTEGER token value.
     * term : factor ((MUL | DIV) factor)*
     *
     * @return int
     */
    public function term() {
        $result = $this->factor();

        while (in_array($this->currentToken->type(), [Token::MULTIPLY, Token::DIVIDE])) {
            $token = $this->currentToken;
            $this->eat($token->type());

            switch ($token->type()) {
                case Token::MULTIPLY:
                    $result *= $this->factor();
                    break;
                case Token::DIVIDE:
                    $result /= $this->factor();
                    break;
            }
        }

        return $result;
    }

    /**
     * Arithmetic expression parser / interpreter.
     *
     * expr   : factor ((PLUS | MINUS | MULTIPLY | DIVIDE) factor)*
     * factor : INTEGER
     *
     * @return int
     */
    public function expression(): int
    {
        $result = $this->term();

        while (\in_array($this->currentToken->type(), [Token::PLUS, Token::MINUS])) {
            $token = $this->currentToken;
            $this->eat($token->type());

            switch ($token->type()) {
                case Token::PLUS:
                    $result += $this->term();
                    break;
                case Token::MINUS:
                    $result -= $this->term();
                    break;
            }
        }

        return (int) $result;
    }
}
