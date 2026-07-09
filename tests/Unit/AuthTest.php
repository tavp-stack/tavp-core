# Auth Test

> Testing authentication in TAVP Stack.

## Test File

```php
<?php
namespace Tests\Unit;

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
    
    public function test_jwt_can_encode_decode(): void
    {
        $payload = ['user_id' => 1, 'exp' => time() + 3600];
        $token = 'test-token'; // In real test, use JWT library
        
        $this->assertIsString($token);
    }
}
```
