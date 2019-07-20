<?php

namespace Bryse\Sora\Parser;

use Bryse\Sora\Exceptions\InterpreterException;
use Bryse\Sora\Lexer;
use Bryse\Sora\Token;

class Parser
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

    public function parse(): Node
    {
        $tree = $this->expression();

        return $tree;
    }

    public function error()
    {
        throw new InterpreterException('Invalid syntax');
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
     * factor : (PLUS|MINUS)factor | INTEGER | LEFT_PARENTHESIS expr RIGHT_PARENTHESIS
     *
     * @return int
     */
    public function factor(): Node
    {
        $token = $this->currentToken;

        if (in_array($token->type(), [Token::PLUS, Token::MINUS])) {
            $this->eat($token->type());
            return new UnaryOperator($token, $this->factor());
        }

        if ($token->type() == Token::INTEGER) {
            $this->eat(Token::INTEGER);

            return new Number($token);
        }

        if (\in_array($token->type(), [Token::LEFT_PARENTHESIS, Token::RIGHT_PARENTHESIS])) {
            $this->eat(Token::LEFT_PARENTHESIS);
            $node = $this->expression();
            $this->eat(Token::RIGHT_PARENTHESIS);

            return $node;
        }

        $this->error();
    }

    /**
     * Returns an INTEGER token value.
     * term : factor ((MULTIPLY | DIVIDE) factor)*
     *
     * @return Node
     */
    public function term(): Node
    {
        $node = $this->factor();

        while (\in_array($this->currentToken->type(), [Token::MULTIPLY, Token::DIVIDE])) {
            $token = $this->currentToken;
            $this->eat($token->type());
            $node = new BinaryOperator($node, $token, $this->factor());
        }

        return $node;
    }

    /**
     * Arithmetic expression parser / interpreter.
     *
     * expr   : term ((PLUS | MINUS) term)*
     * term   : factor ((MULTIPLY | DIVIDE) factor)*
     * factor : (PLUS|MINUS)factor | INTEGER | LEFT_PARENTHESIS expr RIGHT_PARENTHESIS
     *
     * @return Node
     */
    public function expression(): Node
    {
        $node = $this->term();

        while (\in_array($this->currentToken->type(), [Token::PLUS, Token::MINUS])) {
            $token = $this->currentToken;
            $this->eat($token->type());
            $node = new BinaryOperator($node, $token, $this->term());
        }

        return $node;
    }
}
