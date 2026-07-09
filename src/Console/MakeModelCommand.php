<?php

declare(strict_types=1);

namespace Tavp\Core\Console;

use Tavp\Core\Application;

/**
 * tavp make:model — generate a model class with CRUD boilerplate.
 *
 * Usage: tavp make:model <Name> [--migration] [--resource]
 */
class MakeModelCommand
{
    public function handle(array $args): void
    {
        $name = $args[0] ?? null;

        if ($name === null) {
            echo "Usage: tavp make:model <Name>\n";
            return;
        }

        $withMigration = in_array('--migration', $args, true);
        $isResource = in_array('--resource', $args, true);

        $this->createModel($name, $isResource);

        if ($withMigration) {
            $this->createMigration($name);
        }

        echo "Model created successfully.\n";
    }

    private function createModel(string $name, bool $resource): void
    {
        $fileName = $this->toPascalCase($name) . '.php';
        $snakeName = $this->toSnake($name);
        $table = $this->pluralize($snakeName);
        $className = $this->toPascalCase($name);

        $fillables = $resource
            ? "'name',\n        // 'email',"
            : "'name',";

        $content = <<<PHP
<?php

declare(strict_types=1);

namespace Tavp\Core\Database\Models;

use Tavp\Core\Database\Model;

class {$className} extends Model
{
    protected string \$table = '{$table}';

    protected array \$fillable = [
        {$fillables}
    ];

    protected array \$casts = [
        // 'metadata' => 'json',
        // 'is_active' => 'boolean',
    ];
}

PHP;

        $path = base_path('src/Database/Models/' . $fileName);

        if (file_exists($path)) {
            echo "  Model {$fileName} already exists. Skipping.\n";
            return;
        }

        file_put_contents($path, $content);
        echo "  Created src/Database/Models/{$fileName}\n";
    }

    private function createMigration(string $name): void
    {
        $timestamp = date('Y_m_d_His');
        $snakeName = $this->toSnake($name);
        $table = $this->pluralize($snakeName);
        $fileName = "{$timestamp}_create_{$table}_table.php";

        $content = <<<PHP
<?php

declare(strict_types=1);

use Tavp\Core\Database\Migrations\Migration;
use Tavp\Core\Database\Migrations\SchemaBuilder;

return new class extends Migration
{
    public function up(SchemaBuilder \$schema): void
    {
        \$schema->createTable('{$table}', function (SchemaBuilder\TableDefinition \$table) {
            \$table->add(\$schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            \$table->add(\$schema->column('name', 'string'));
            \$table->add(\$schema->column('created_at', 'timestamp'));
            \$table->add(\$schema->column('updated_at', 'timestamp'));
        });
    }

    public function down(SchemaBuilder \$schema): void
    {
        \$schema->dropTable('{$table}');
    }
};

PHP;

        $path = base_path('database/migrations/' . $fileName);

        if (file_exists($path)) {
            echo "  Migration {$fileName} already exists. Skipping.\n";
            return;
        }

        file_put_contents($path, $content);
        echo "  Created database/migrations/{$fileName}\n";
    }

    private function toPascalCase(string $value): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $value)));
    }

    private function toSnake(string $value): string
    {
        $result = preg_replace('/(?<!^)[A-Z]/', '_$0', $value);

        return strtolower((string) $result);
    }

    private function pluralize(string $value): string
    {
        $irregular = ['person' => 'people', 'man' => 'men', 'woman' => 'women'];

        if (isset($irregular[$value])) {
            return $irregular[$value];
        }

        if (str_ends_with($value, 'y') && !in_array(substr($value, -2, 1), ['a', 'e', 'i', 'o', 'u'], true)) {
            return substr($value, 0, -1) . 'ies';
        }

        if (str_ends_with($value, 's') || str_ends_with($value, 'x') || str_ends_with($value, 'z')
            || str_ends_with($value, 'ch') || str_ends_with($value, 'sh')) {
            return $value . 'es';
        }

        return $value . 's';
    }
}
