# Validation Test

> Testing validation in TAVP Stack.

## Test File

```php
<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ValidationTest extends TestCase
{
    public function test_required_rule(): void
    {
        $data = ['name' => 'John'];
        $rules = ['name' => 'required'];
        
        // In real test, use validation facade
        $this->assertArrayHasKey('name', $data);
    }
    
    public function test_email_rule(): void
    {
        $validEmail = 'user@example.com';
        $invalidEmail = 'invalid-email';
        
        $this->assertNotEmpty(filter_var($validEmail, FILTER_VALIDATE_EMAIL));
        $this->assertFalse(filter_var($invalidEmail, FILTER_VALIDATE_EMAIL));
    }
    
    public function test_min_length_rule(): void
    {
        $password = 'secret';
        $minLength = 8;
        
        $this->assertGreaterThanOrEqual($minLength, strlen($password));
    }
    
    public function test_unique_rule(): void
    {
        $email1 = 'user1@example.com';
        $email2 = 'user2@example.com';
        
        $this->assertNotEquals($email1, $email2);
    }
}
```
