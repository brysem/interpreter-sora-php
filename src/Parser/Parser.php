<?php

namespace Bryse\Sora\Parser;

use Bryse\Sora\Exceptions\InterpreterException;
use Bryse\Sora\Lexer;
use Bryse\Sora\Token;
use Bryse\Sora\Parser\Nodes\Number;
use Bryse\Sora\Parser\Nodes\UnaryOperator;
use Bryse\Sora\Parser\Nodes\BinaryOperator;
use Bryse\Sora\Parser\Nodes\Node;
use Bryse\Sora\Parser\Nodes\NoOperation;
use Bryse\Sora\Parser\Nodes\Variable;
use Bryse\Sora\Parser\Nodes\Assignment;
use Bryse\Sora\Parser\Nodes\Compound;

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
        $tree = $this->program();

        if ($this->currentToken->type() != Token::EOF) {
            $this->error();
        }

        return $tree;
    }

    protected function error()
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
    protected function eat($tokenType)
    {
        $tokenType = ! \is_array($tokenType) ? [$tokenType] : $tokenType;

        if (! \in_array($this->currentToken->type(), $tokenType)) {
            $this->error();
        }

        $this->currentToken = $this->lexer->getNextToken();
    }

    /**
     * Returns an INTEGER token value.
     * factor : (PLUS|MINUS)factor | INTEGER | LEFT_PARENTHESIS expr RIGHT_PARENTHESIS. | variable
     *
     * @return int
     */
    protected function factor(): Node
    {
        $token = $this->currentToken;

        if (\in_array($token->type(), [Token::PLUS, Token::MINUS])) {
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

        if ($token->type() == Token::ID) {
            return $this->variable();
        }

        $this->error();
    }

    /**
     * Returns an INTEGER token value.
     * term : factor ((MULTIPLY | DIVIDE) factor)*.
     *
     * @return Node
     */
    protected function term(): Node
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
    protected function expression(): Node
    {
        $node = $this->term();

        while (\in_array($this->currentToken->type(), [Token::PLUS, Token::MINUS])) {
            $token = $this->currentToken;
            $this->eat($token->type());
            $node = new BinaryOperator($node, $token, $this->term());
        }

        return $node;
    }

    /**
     * An empty production
     *
     * @return Node
     */
    protected function empty(): Node
    {
        return new NoOperation();
    }

    /**
     * variable : ID
     *
     * @return Node
     */
    protected function variable(): Node
    {
        $node = new Variable($this->currentToken);
        $this->eat(Token::ID);

        return $node;
    }

    /**
     * assignment_statement : variable ASSIGN expr
     *
     * @return Node
     */
    protected function assignmentStatement(): Node
    {
        $left = $this->variable();
        $token = $this->currentToken;
        $this->eat(Token::ASSIGNMENT);
        $right = $this->expression();

        return new Assignment($left, $token, $right);
    }

    /**
    * statement : compound_statement | assignment_statement | empty
     *
     * @return Node
     */
    protected function statement(): Node
    {
        if ($this->currentToken->type() == Token::BEGIN) {
            return $this->compoundStatement();
        }

        if ($this->currentToken->type() == Token::ID) {
            return $this->assignmentStatement();
        }

        return $this->empty();
    }

    /**
     *     statement_list : statement | statement SEMI statement_list
     *
     * @return Node[]
     */
    protected function statementList(): array
    {
        $node = $this->statement();
        $results = [$node];

        while ($this->currentToken->type() == Token::SEMICOLON) {
            $this->eat(Token::SEMICOLON);
            $results[] = $this->statement();
        }

        if ($this->currentToken->type() == Token::ID) {
            $this->error();
        }

        return $results;
    }

    /**
     * compound_statement: BEGIN statement_list END.
     *
     * @return Node
     */
    protected function compoundStatement(): Node
    {
        $this->eat(Token::BEGIN);
        $nodes = $this->statementList();
        $this->eat(Token::END);

        $root = new Compound($nodes);

        return $root;
    }

    /**
     * program : compound_statement DOT
     *
     * @return Node
     */
    protected function program(): Node
    {
        $node = $this->compoundStatement();
        $this->eat(Token::DOT);

        return $node;
    }
}
