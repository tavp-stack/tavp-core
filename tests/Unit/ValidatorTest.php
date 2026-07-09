<?php

declare(strict_types=1);

use Tavp\Core\Validation\Validator;
use PHPUnit\Framework\TestCase;

/**
 * Tests the validator rules. Pure PHP.
 */
class ValidatorTest extends TestCase
{
    public function testRequiredPassesWhenPresent(): void
    {
        $v = new Validator();
        $this->assertTrue($v->validate(['name' => 'John'], ['name' => 'required']));
    }

    public function testRequiredFailsWhenMissing(): void
    {
        $v = new Validator();
        $this->assertFalse($v->validate([], ['name' => 'required']));
        $this->assertArrayHasKey('name', $v->errors());
    }

    public function testEmailRule(): void
    {
        $v = new Validator();
        $this->assertTrue($v->validate(['email' => 'a@b.com'], ['email' => 'required|email']));
        $this->assertFalse($v->validate(['email' => 'nope'], ['email' => 'required|email']));
    }

    public function testIntegerRule(): void
    {
        $v = new Validator();
        $this->assertTrue($v->validate(['age' => '30'], ['age' => 'integer']));
        $this->assertFalse($v->validate(['age' => 'abc'], ['age' => 'integer']));
    }

    public function testMinRuleOnString(): void
    {
        $v = new Validator();
        $this->assertTrue($v->validate(['name' => 'abcdef'], ['name' => 'min:3']));
        $this->assertFalse($v->validate(['name' => 'ab'], ['name' => 'min:3']));
    }

    public function testInRule(): void
    {
        $v = new Validator();
        $this->assertTrue($v->validate(['role' => 'admin'], ['role' => 'in:admin,user']));
        $this->assertFalse($v->validate(['role' => 'guest'], ['role' => 'in:admin,user']));
    }
}
