<?php

namespace Bryse\Sora;

use Bryse\Sora\Exceptions\InterpreterException;
use Bryse\Sora\Parser\NodeVisitor;
use Bryse\Sora\Parser\BinaryOperator;
use Bryse\Sora\Parser\Number;
use Bryse\Sora\Parser\Parser;

class Interpreter extends NodeVisitor
{
    /**
     * The parser instance parsing the code.
     *
     * @var Parser
     */
    protected $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public static function run(string $code)
    {
        $lexer = new Lexer($code);
        $parser = new Parser($lexer);
        $interpreter = new self($parser);

        $result = $interpreter->interpret();

        return $result;
    }

    public function interpret()
    {
        $tree = $this->parser->parse();

        return $this->visit($tree);
    }

    public function visitBinaryOperator(BinaryOperator $node)
    {
        $left = $this->visit($node->left());
        $right = $this->visit($node->right());

        switch($node->token()->type()) {
            case Token::PLUS:
                return $left + $right;
            case Token::MINUS:
                return $left - $right;
            case Token::MULTIPLY:
                return $left * $right;
            case Token::DIVIDE:
                return $left / $right;
        }
    }

    public function visitNumber(Number $node)
    {
        return $node->value();
    }
}
