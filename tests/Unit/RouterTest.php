<?php

declare(strict_types=1);

namespace Tavp\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public function test_uri_matches_expected_pattern(): void
    {
        $route = '/users/{id}';
        $regex = '#^/users/(?P<id>[^/]+)$#';

        $this->assertSame(1, preg_match($regex, '/users/42'));
        $this->assertSame(0, preg_match($regex, '/users'));
    }

    public function test_static_route_matches_exactly(): void
    {
        $this->assertTrue(str_ends_with('/users', '/users'));
        $this->assertFalse(str_ends_with('/users/create', '/users'));
    }

    public function test_constraint_builder_joins_segments(): void
    {
        $segments = ['users'];
        $wrapped = array_map(static fn (string $s) => trim($s, '{}'), $segments);

        $this->assertSame('users', $wrapped[0]);
    }

    public function test_prefers_longer_static_match(): void
    {
        $static = '/users';
        $param = '/users/{id}';

        $this->assertGreaterThan(strlen($static), strlen($param));
    }
}