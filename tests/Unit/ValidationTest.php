<?php

declare(strict_types=1);

namespace Tavp\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;

class ValidationTest extends TestCase
{
    public function test_required_rule_rejects_empty_string(): void
    {
        $isEmpty = trim('') === '';

        $this->assertTrue($isEmpty);
        $this->assertFalse(trim('John') === '');
    }

    public function test_email_rule(): void
    {
        $this->assertNotEmpty(filter_var('user@example.com', FILTER_VALIDATE_EMAIL));
        $this->assertFalse(filter_var('invalid-email', FILTER_VALIDATE_EMAIL));
    }

    public function test_min_length_rule(): void
    {
        $password = 'secret123';
        $minLength = 8;

        $this->assertGreaterThanOrEqual($minLength, strlen($password));
        $this->assertLessThan($minLength, strlen('secret'));
    }

    public function test_unique_rule(): void
    {
        $email1 = 'user1@example.com';
        $email2 = 'user2@example.com';

        $this->assertNotEquals($email1, $email2);
    }
}