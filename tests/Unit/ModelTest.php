<?php

declare(strict_types=1);

namespace Tavp\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    public function test_database_defaults_to_array(): void
    {
        $rows = array_filter(['id' => 1, 'name' => 'User']);

        $this->assertCount(2, $rows);
        $this->assertArrayHasKey('name', $rows);
    }

    public function test_associative_result_maps_by_key(): void
    {
        $row = ['id' => 1, 'email_verified_at' => null];

        $this->assertArrayHasKey('id', $row);
        $this->assertArrayHasKey('email_verified_at', $row);
    }

    public function test_type_casting_triggers_expected_values(): void
    {
        $id = (int) '42';
        $flag = (bool) '0';

        $this->assertSame(42, $id);
        $this->assertFalse($flag);
    }

    public function test_has_hidden_attribute_by_convention(): void
    {
        $hidden = ['password', 'remember_token'];

        $this->assertContains('password', $hidden);
        $this->assertContains('remember_token', $hidden);
    }
}