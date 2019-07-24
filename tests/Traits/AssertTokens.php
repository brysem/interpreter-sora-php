<?php

namespace Bryse\Sora\Tests\Traits;

use Bryse\Sora\Token;
use Bryse\Sora\Lexer;

/**
 * Trait AssertTokens
 *
 * @mixin \PHPUnit\Framework\TestCase
 */
trait AssertTokens
{
    /**
     * Assert that the lexer's next token is equal to the expected token
     *
     * @param Lexer $lexer
     * @param Token $expectedToken
     */
    public function assertToken(Lexer $lexer, Token $expectedToken)
    {
        $lexerToken = $lexer->getNextToken();
        self::assertEquals($expectedToken->type(), $lexerToken->type(), sprintf("Expected token of type %s but got %s instead", $expectedToken, $lexerToken));
        self::assertEquals($expectedToken->value(), $lexerToken->value(), sprintf("Expected token with value %s but got %s instead", $expectedToken, $lexerToken));
    }
}
