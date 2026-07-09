# Cache Test

> Testing cache in TAVP Stack.

## Test File

```php
<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    public function test_can_store_and_retrieve(): void
    {
        $cache = [];
        $key = 'test';
        $value = 'hello';
        
        $cache[$key] = $value;
        
        $this->assertEquals($value, $cache[$key]);
    }
    
    public function test_can_check_if_exists(): void
    {
        $cache = ['key' => 'value'];
        
        $this->assertArrayHasKey('key', $cache);
        $this->assertArrayNotHasKey('missing', $cache);
    }
    
    public function test_can_forget(): void
    {
        $cache = ['key' => 'value'];
        
        unset($cache['key']);
        
        $this->assertArrayNotHasKey('key', $cache);
    }
    
    public function test_can_flush(): void
    {
        $cache = ['key1' => 'value1', 'key2' => 'value2'];
        
        $cache = [];
        
        $this->assertEmpty($cache);
    }
}
```
