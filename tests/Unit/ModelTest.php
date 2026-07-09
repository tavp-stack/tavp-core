# Model Test

> Testing the TAVP ORM model.

## Test File

```php
<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    public function test_model_has_table(): void
    {
        $model = new \App\Models\User();
        $this->assertEquals('users', $model->getTable());
    }
    
    public function test_model_has_fillable(): void
    {
        $model = new \App\Models\User();
        $this->assertContains('name', $model->getFillable());
    }
    
    public function test_model_has_hidden(): void
    {
        $model = new \App\Models\User();
        $this->assertContains('password', $model->getHidden());
    }
    
    public function test_model_has_casts(): void
    {
        $model = new \App\Models\User();
        $casts = $model->getCasts();
        $this->assertArrayHasKey('email_verified_at', $casts);
    }
}
```
