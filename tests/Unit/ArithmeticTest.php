<?php

namespace Bryse\Sora\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Bryse\Sora\Interpreter;

class ArithmeticTest extends TestCase
{
    public function testSingleDigitAddition()
    {
        $this->assertEquals(7, Interpreter::run('3+4'));
        $this->assertEquals(8, Interpreter::run('3+5'));
        $this->assertEquals(12, Interpreter::run('3+9'));
    }

    public function testDoubleDigitAddition()
    {
        $this->assertEquals(33, Interpreter::run('11+22'));
        $this->assertEquals(15, Interpreter::run('10+5'));
        $this->assertEquals(80, Interpreter::run('75+5'));
    }

    public function testMultipleDigitsAddition()
    {
        $this->assertEquals(17118, Interpreter::run('15159+1959'));
        $this->assertEquals(999, Interpreter::run('99+900'));
        $this->assertEquals(2674, Interpreter::run('1337+1337'));
    }

    public function testSingleDigitSubtraction()
    {
        $this->assertEquals(-1, Interpreter::run('3-4'));
        $this->assertEquals(3, Interpreter::run('5-2'));
        $this->assertEquals(1, Interpreter::run('9-8'));
    }

    public function testDoubleDigitSubtraction()
    {
        $this->assertEquals(26, Interpreter::run('30-4'));
        $this->assertEquals(-5, Interpreter::run('5-10'));
        $this->assertEquals(10, Interpreter::run('90-80'));
    }

    public function testMultipleDigitsSubtraction()
    {
        $this->assertEquals(0, Interpreter::run('1959-1959'));
        $this->assertEquals(-801, Interpreter::run('99-900'));
        $this->assertEquals(1182, Interpreter::run('1337-155'));
    }

    public function testMultiplication()
    {
        $this->assertEquals(9, Interpreter::run('3*3'));
        $this->assertEquals(5, Interpreter::run('1*5'));
        $this->assertEquals(48, Interpreter::run('8*6'));
        $this->assertEquals(4335, Interpreter::run('85*51'));
        $this->assertEquals(4356, Interpreter::run('121*36'));
        $this->assertEquals(0, Interpreter::run('9101*0'));
    }

    public function testDivision()
    {
        $this->assertEquals(1, Interpreter::run('3/3'));
        $this->assertEquals(4, Interpreter::run('20/5'));
        $this->assertEquals(100, Interpreter::run('1000/10'));
    }

    public function testWhitespace()
    {
        $this->assertEquals(1, Interpreter::run('3 / 3'));
        $this->assertEquals(100, Interpreter::run('20 * 5'));
        $this->assertEquals(100, Interpreter::run('10  *  10'));
    }

    public function testArbirtrayAmountOfOperators()
    {
        $this->assertEquals(18, Interpreter::run('9 - 5 + 3 + 11'));
        $this->assertEquals(10, Interpreter::run('3 * 5 + 5 - 10'));
        $this->assertEquals(20, Interpreter::run('10 / 5 + 8 + 10'));
        $this->assertEquals(17, Interpreter::run('14 + 2 * 3 - 6 / 2'));
        $this->assertEquals(50, Interpreter::run('15 + 5 * 3 + 20'));
    }

    public function testNestedExpressions()
    {
        $this->assertEquals(2, Interpreter::run('(1 + 1)'));
        $this->assertEquals(6, Interpreter::run('3 * (1 + 1)'));
        $this->assertEquals(12, Interpreter::run('(1 + 1) / 2 * (6 * 2)'));
        $this->assertEquals(22, Interpreter::run('7 + 3 * (10 / (12 / (3 + 1) - 1))'));
        $this->assertEquals(10, Interpreter::run('7 + 3 * (10 / (12 / (3 + 1) - 1)) / (2 + 3) - 5 - 3 + (8)'));
        $this->assertEquals(12, Interpreter::run('7 + (((3 + 2)))'));
    }
}
