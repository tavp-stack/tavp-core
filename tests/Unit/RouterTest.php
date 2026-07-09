# Router Test

> Testing the TAVP router.

## Test File

```php
<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tavp\Routing\Router;

class RouterTest extends TestCase
{
    public function test_can_add_get_route(): void
    {
        $router = new Router();
        
        $router->get('/users', function () {
            return 'users';
        });
        
        $this->assertNotEmpty($router->getRoutes());
    }
    
    public function test_can_add_post_route(): void
    {
        $router = new Router();
        
        $router->post('/users', function () {
            return 'create user';
        });
        
        $this->assertNotEmpty($router->getRoutes());
    }
    
    public function test_can_add_route_with_parameters(): void
    {
        $router = new Router();
        
        $router->get('/users/{id}', function ($id) {
            return "User {$id}";
        });
        
        $this->assertNotEmpty($router->getRoutes());
    }
    
    public function test_can_group_routes(): void
    {
        $router = new Router();
        
        $router->group(['prefix' => 'api'], function ($router) {
            $router->get('/users', function () {
                return 'api users';
            });
        });
        
        $this->assertNotEmpty($router->getRoutes());
    }
    
    public function test_can_name_route(): void
    {
        $router = new Router();
        
        $router->get('/users', function () {
            return 'users';
        })->name('users.index');
        
        $this->assertNotEmpty($router->getRoutes());
    }
}
```
