<?php

declare(strict_types=1);

namespace Tavp\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tavp\Core\Database\Model;

class ModelTest extends TestCase
{
    public function test_model_has_default_primary_key(): void
    {
        $model = new class extends Model {
            protected string $table = 'test';
        };

        $this->assertEquals('id', $model::getPrimaryKey());
    }

    public function test_model_fill_respects_fillable(): void
    {
        $model = new class extends Model {
            protected string $table = 'test';
            protected array $fillable = ['name', 'email'];
        };

        $model->fill([
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => 'secret', // should not be set
        ]);

        $this->assertEquals('John', $model->name);
        $this->assertEquals('john@example.com', $model->email);
    }

    public function test_model_cast_boolean(): void
    {
        $model = new class extends Model {
            protected string $table = 'test';
            protected array $fillable = ['is_active'];
            protected array $casts = ['is_active' => 'boolean'];
        };

        $model->fill(['is_active' => 1]);
        $this->assertIsBool($model->is_active);
        $this->assertTrue($model->is_active);
    }

    public function test_model_cast_integer(): void
    {
        $model = new class extends Model {
            protected string $table = 'test';
            protected array $fillable = ['count'];
            protected array $casts = ['count' => 'integer'];
        };

        $model->fill(['count' => '42']);
        $this->assertIsInt($model->count);
        $this->assertEquals(42, $model->count);
    }

    public function test_model_cast_json(): void
    {
        $model = new class extends Model {
            protected string $table = 'test';
            protected array $fillable = ['metadata'];
            protected array $casts = ['metadata' => 'json'];
        };

        $model->fill(['metadata' => '{"key":"value"}']);
        $this->assertIsArray($model->metadata);
        $this->assertEquals('value', $model->metadata['key']);
    }
}
