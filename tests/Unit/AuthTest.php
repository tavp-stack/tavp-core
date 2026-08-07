<?php

declare(strict_types=1);

namespace Tavp\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    public function test_can_hash_password(): void
    {
        $password = 'secret';
        $hashed = password_hash($password, PASSWORD_BCRYPT);

        $this->assertNotEquals($password, $hashed);
        $this->assertTrue(password_verify($password, $hashed));
    }

    public function test_can_verify_password(): void
    {
        $password = 'secret';
        $hashed = password_hash($password, PASSWORD_BCRYPT);

        $this->assertTrue(password_verify($password, $hashed));
        $this->assertFalse(password_verify('wrong', $hashed));
    }
}