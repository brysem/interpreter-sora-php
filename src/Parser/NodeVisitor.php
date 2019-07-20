<?php

namespace Bryse\Sora\Parser;

use Exception;

class NodeVisitor
{
    public function visit(Node $node)
    {
        $methodName = $this->getNodeMethodName($node);

        if (! method_exists($this, $methodName)) {
            $this->genericVisit($node);
        }

        return $this->$methodName($node);
    }

    public function genericVisit(Node $node)
    {
        throw new Exception("No {$this->getNodeMethodName($node)} method");
    }

    public function getNodeMethodName(Node $node)
    {
        $className = (new \ReflectionClass($node))->getShortName();

        return "visit{$className}";
    }
}
