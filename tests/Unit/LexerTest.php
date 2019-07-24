<?php

namespace Bryse\Sora\Tests\Unit;

use Bryse\Sora\Lexer;
use Bryse\Sora\Tests\Traits\AssertTokens;
use Bryse\Sora\Token;
use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase
{
    use AssertTokens;

    public function testSimpleAdditionTokens()
    {
        $lexer = new Lexer('2 + 5');
        $this->assertToken($lexer, new Token(Token::INTEGER, 2));
        $this->assertToken($lexer, new Token(Token::PLUS, '+'));
        $this->assertToken($lexer, new Token(Token::INTEGER, 5));
    }

    public function testDivisionTokens()
    {
        $lexer = new Lexer('10 / 5 + 8 + 10');
        $this->assertToken($lexer, new Token(Token::INTEGER, 10));
        $this->assertToken($lexer, new Token(Token::DIVIDE, '/'));
        $this->assertToken($lexer, new Token(Token::INTEGER, 5));
        $this->assertToken($lexer, new Token(Token::PLUS, '+'));
        $this->assertToken($lexer, new Token(Token::INTEGER, '8'));
        $this->assertToken($lexer, new Token(Token::PLUS, '+'));
        $this->assertToken($lexer, new Token(Token::INTEGER, '10'));
    }

    public function testNestedTokens()
    {
        $lexer = new Lexer('3 + 5 * (10 - 9)');
        $this->assertToken($lexer, new Token(Token::INTEGER, 3));
        $this->assertToken($lexer, new Token(Token::PLUS, '+'));
        $this->assertToken($lexer, new Token(Token::INTEGER, 5));
        $this->assertToken($lexer, new Token(Token::MULTIPLY, '*'));
        $this->assertToken($lexer, new Token(Token::LEFT_PARENTHESIS, '('));
        $this->assertToken($lexer, new Token(Token::INTEGER, 10));
        $this->assertToken($lexer, new Token(Token::MINUS, '-'));
        $this->assertToken($lexer, new Token(Token::INTEGER, 9));
        $this->assertToken($lexer, new Token(Token::RIGHT_PARENTHESIS, ')'));
    }

    public function testVariableAssignmentToken()
    {
        $lexer = new Lexer('BEGIN a := 2; END.');
        $this->assertToken($lexer, new Token('BEGIN', 'BEGIN'));
        $this->assertToken($lexer, new Token('ID', 'a'));
        $this->assertToken($lexer, new Token(Token::ASSIGNMENT, ':='));
        $this->assertToken($lexer, new Token(Token::INTEGER, 2));
        $this->assertToken($lexer, new Token(Token::SEMICOLON, ';'));
        $this->assertToken($lexer, new Token('END', 'END'));
        $this->assertToken($lexer, new Token(Token::DOT, '.'));
    }
}
