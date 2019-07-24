<?php

namespace Bryse\Sora\Parser\Nodes;

use Exception;
use Bryse\Sora\Parser\Parser;
use Bryse\Sora\Support\Str;

class NodeVisualizer extends NodeVisitor
{
    /**
     * The parser instance
     *
     * @var Parser
     */
    protected $parser;

    /**
     * The node count
     *
     * @var int
     */
    protected $nodeCount = 1;

    /**
     * The dot headers
     *
     * @var string[]
     */
    protected $dotHeader = [];

    /**
     * The dot bodies
     *
     * @var string[]
     */
    protected $dotBody = [];

    /**
     * The dot footers
     *
     * @var string[]
     */
    protected $dotFooter = [];

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
        $this->nodeCount = 1;
        $this->dotHeader = [Str::dedent('
            digraph astgraph {
                node [shape=circle, fontsize=12, fontname="Courier", height=.1];
                ranksep=.3;
                edge [arrowsize=.5]
        ')];
        $this->dotBody = [];
        $this->dotFooter = ['}'];
    }

    public function visitNumber(Number $node)
    {
        $string = sprintf('  node%s [label="%s"]', $this->nodeCount, $node->token()->value()). PHP_EOL;
        $this->dotBody[] = $string;
        $node->_num = $this->nodeCount;
        $this->nodeCount++;
    }

    public function visitBinaryOperator(BinaryOperator $node)
    {
        $string = sprintf('  node%s [label="%s"]', $this->nodeCount, $node->operator()->value()). PHP_EOL;
        $this->dotBody[] = $string;
        $node->_num = $this->nodeCount;
        $this->nodeCount++;

        $this->visit($node->left());
        $this->visit($node->right());

        foreach([$node->left(), $node->right()] as $childNode) {
            $string = sprintf('  node%s -> node%s', $node->_num, $childNode->_num). PHP_EOL;
            $this->dotBody[] = $string;
        }
    }

    public function visitUnaryOperator(UnaryOperator $node)
    {
        $string = sprintf('  node%s [label="unary %s"]', $this->nodeCount, $node->operator()->value()). PHP_EOL;
        $this->dotBody[] = $string;
        $node->_num = $this->nodeCount;
        $this->nodeCount++;

        $this->visit($node->expression());

        $string = sprintf('  node%s -> node%s', $node->_num, $node->expression()->_num). PHP_EOL;
        $this->dotBody[] = $string;
    }

    public function visitCompound(Compound $node)
    {
        $string = sprintf('  node%s [label="Compound"]', $this->nodeCount). PHP_EOL;
        $this->dotBody[] = $string;
        $node->_num = $this->nodeCount;
        $this->nodeCount++;


        foreach($node->children() as $childNode) {
            $this->visit($childNode);
            $string = sprintf('  node%s -> node%s', $node->_num, $childNode->_num). PHP_EOL;
            $this->dotBody[] = $string;
        }
    }

    public function visitAssignment(Assignment $node)
    {
        $string = sprintf('  node%s [label="%s"]', $this->nodeCount, $node->operator()->value()). PHP_EOL;
        $this->dotBody[] = $string;
        $node->_num = $this->nodeCount;
        $this->nodeCount++;

        $this->visit($node->left());
        $this->visit($node->right());

        foreach([$node->left(), $node->right()] as $childNode) {
            $string = sprintf('  node%s -> node%s', $node->_num, $childNode->_num). PHP_EOL;
            $this->dotBody[] = $string;
        }
    }

    public function visitVariable(Variable $node)
    {
        $string = sprintf('  node%s [label="%s"]', $this->nodeCount, $node->value()). PHP_EOL;
        $this->dotBody[] = $string;
        $node->_num = $this->nodeCount;
        $this->nodeCount++;
    }

    public function visitNoOperation(NoOperation $node)
    {
        $string = sprintf('  node%s [label="NoOp"]', $this->nodeCount). PHP_EOL;
        $this->dotBody[] = $string;
        $node->_num = $this->nodeCount;
        $this->nodeCount++;
    }

    public function generateDot()
    {
        $tree = $this->parser->parse();
        $this->visit($tree);

        return implode('', array_merge($this->dotHeader, $this->dotBody, $this->dotFooter));
    }
}
