<?php

declare(strict_types=1);

namespace Tavp\Core\Console\Commands;

use RuntimeException;

/**
 * tavp make:scaffold — generate model, controller, views, migration, routes
 *
 * Usage:
 *   tavp make:scaffold Post --fields="title:string,body:text,user_id:integer"
 */
class MakeScaffoldCommand
{
    public function execute(array $arguments): void
    {
        $name = $arguments[0] ?? null;

        if (!$name) {
            throw new RuntimeException("Usage: tavp make:scaffold <Name> --fields=\"field:type,...\"");
        }

        $fields = $this->parseFields($arguments['--fields'] ?? '');

        echo "Generating scaffold for: {$name}\n";
        echo "Fields: " . implode(', ', array_keys($fields)) . "\n\n";

        // 1. Generate Model
        $this->generateModel($name, $fields);

        // 2. Generate Migration
        $this->generateMigration($name, $fields);

        // 3. Generate Controller
        $this->generateController($name, $fields);

        // 4. Generate Views
        $this->generateViews($name, $fields);

        // 5. Generate Routes
        $this->generateRoutes($name, $fields);

        echo "\n✓ Scaffold for '{$name}' generated successfully!\n";
    }

    private function parseFields(string $input): array
    {
        $fields = [];
        $parts = explode(',', $input);

        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part)) continue;

            [$fieldName, $fieldType] = explode(':', $part . ':string');
            $fields[$fieldName] = $fieldType;
        }

        return $fields;
    }

    private function generateModel(string $name, array $fields): void
    {
        $className = $this->studly($name);
        $table = $this->snake($this->plural($name));

        $fillable = implode(', ', array_map(fn($f) => "'{$f}'", array_keys($fields)));

        $content = "<?php\n\ndeclare(strict_types=1);\n\nnamespace Tavp\\Core\\Database;\n\nuse Phalcon\\Mvc\\Model;\n\n/**\n * {$className} model.\n */\nclass {$className} extends Model\n{\n    protected string \$table = '{$table}';\n\n    public array \$fillable = [{$fillable}];\n\n    public function initialize(): void\n    {\n        \$this->setSource('{$table}');\n    }\n}\n";

        $path = base_path("app/Models/{$className}.php");
        $this->writeFile($path, $content);
        echo "  ✓ Model: {$className}\n";
    }

    private function generateMigration(string $name, array $fields): void
    {
        $table = $this->snake($this->plural($name));
        $timestamp = date('Y_m_d_His');
        $className = 'Create' . $this->plural($name) . 'Table';

        $columns = '';
        foreach ($fields as $field => $type) {
            $phalconType = match ($type) {
                'string' => 'string',
                'text' => 'text',
                'integer', 'int' => 'integer',
                'boolean', 'bool' => 'boolean',
                'datetime' => 'datetime',
                'decimal' => 'decimal',
                default => 'string',
            };
            $columns .= "            \$table->{$field}('{$field}', '{$phalconType}');\n";
        }

        $content = "<?php\n\ndeclare(strict_types=1);\n\nuse Tavp\\Core\\Database\\Migrations\\SchemaBuilder;\n\n/**\n * Migration: Create {$table} table.\n */\nclass {$className}\n{\n    public function up(SchemaBuilder \$schema): void\n    {\n        \$schema->createTable('{$table}', function (\$table) {\n            \$table->add(\$schema->column('id', 'integer', ['identity' => true, 'primary' => true]));\n{$columns}            \$table->add(\$schema->column('created_at', 'datetime', ['null' => true]));\n            \$table->add(\$schema->column('updated_at', 'datetime', ['null' => true]));\n        });\n    }\n\n    public function down(SchemaBuilder \$schema): void\n    {\n        \$schema->dropTable('{$table}');\n    }\n}\n";

        $path = base_path("database/migrations/{$timestamp}_create_{$table}_table.php");
        $this->writeFile($path, $content);
        echo "  ✓ Migration: {$timestamp}_create_{$table}_table\n";
    }

    private function generateController(string $name, array $fields): void
    {
        $className = $this->studly($name) . 'Controller';

        $content = "<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Controllers;\n\nuse Tavp\\Core\\Http\\Controller;\nuse App\\Models\\{$this->studly($name)};\n\n/**\n * {$this->studly($name)} resource controller.\n */\nclass {$className} extends Controller\n{\n    public function index(): string\n    {\n        \$items = {$this->studly($name)}::all();\n        return \$this->view('{$this->snake($name)}/index', ['items' => \$items]);\n    }\n\n    public function show(int \$id): string\n    {\n        \$item = {$this->studly($name)}::findFirst(\$id);\n        return \$this->view('{$this->snake($name)}/show', ['item' => \$item]);\n    }\n\n    public function create(): string\n    {\n        return \$this->view('{$this->snake($name)}/create');\n    }\n\n    public function store(): void\n    {\n        \$item = new {$this->studly($name)}();\n        \$item->save(\$this->request->getPost());\n        \$this->response->redirect('/{$this->snake($this->plural($name))}');\n    }\n\n    public function edit(int \$id): string\n    {\n        \$item = {$this->studly($name)}::findFirst(\$id);\n        return \$this->view('{$this->snake($name)}/edit', ['item' => \$item]);\n    }\n\n    public function update(int \$id): void\n    {\n        \$item = {$this->studly($name)}::findFirst(\$id);\n        \$item->update(\$this->request->getPost());\n        \$this->response->redirect('/{$this->snake($this->plural($name))}');\n    }\n\n    public function destroy(int \$id): void\n    {\n        \$item = {$this->studly($name)}::findFirst(\$id);\n        \$item->delete();\n        \$this->response->redirect('/{$this->snake($this->plural($name))}');\n    }\n}\n";

        $path = base_path("app/Controllers/{$className}.php");
        $this->writeFile($path, $content);
        echo "  ✓ Controller: {$className}\n";
    }

    private function generateViews(string $name, array $fields): void
    {
        $dir = $this->snake($name);
        $dirPath = base_path("resources/views/{$dir}");
        $this->ensureDir($dirPath);

        $views = ['index', 'show', 'create', 'edit'];
        foreach ($views as $view) {
            $content = $this->getViewTemplate($name, $view, $fields);
            $path = "{$dirPath}/{$view}.volt";
            $this->writeFile($path, $content);
            echo "  ✓ View: {$dir}/{$view}.volt\n";
        }
    }

    private function generateRoutes(string $name, array $fields): void
    {
        $snake = $this->snake($this->plural($name));
        $studly = $this->studly($name) . 'Controller';

        $content = "<?php\n\n\$router->addGet('/{$snake}', [{$studly}::class, 'index']);\n\$router->addGet('/{$snake}/create', [{$studly}::class, 'create']);\n\$router->addPost('/{$snake}', [{$studly}::class, 'store']);\n\$router->addGet('/{$snake}/{id:int}', [{$studly}::class, 'show']);\n\$router->addGet('/{$snake}/{id:int}/edit', [{$studly}::class, 'edit']);\n\$router->addPost('/{$snake}/{id:int}', [{$studly}::class, 'update']);\n\$router->addPost('/{$snake}/{id:int}/delete', [{$studly}::class, 'destroy']);\n";

        $path = base_path("routes/{$snake}.php");
        $this->writeFile($path, $content);
        echo "  ✓ Routes: {$snake}.php\n";
    }

    private function getViewTemplate(string $name, string $view, array $fields): string
    {
        $snake = $this->snake($name);
        $studly = $this->studly($name);

        return match ($view) {
            'index' => <<<VOLT
{% extends 'layouts/app.volt' %}

{% block content %}
<div class="max-w-7xl mx-auto py-8 px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">{{ '{$studly}' }}</h1>
        <a href="/{$snake}/create" class="bg-blue-600 text-white px-4 py-2 rounded">Create</a>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
VOLT . $this->getTableHeaders($fields) . <<<VOLT
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                {% for item in items %}
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ item.id }}</td>
VOLT . $this->getTableCells($fields, $snake) . <<<VOLT
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="/{$snake}/{{ item.id }}" class="text-blue-600 hover:underline">View</a>
                        <a href="/{$snake}/{{ item.id }}/edit" class="text-yellow-600 hover:underline ml-2">Edit</a>
                    </td>
                </tr>
                {% endfor %}
            </tbody>
        </table>
    </div>
</div>
{% endblock %}
VOLT,
            'show' => <<<VOLT
{% extends 'layouts/app.volt' %}

{% block content %}
<div class="max-w-3xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-4">{{ '{$studly}' }} Details</h1>

    <div class="bg-white shadow rounded-lg p-6">
VOLT . $this->getDetailFields($fields) . <<<VOLT
    </div>

    <div class="mt-4 flex gap-2">
        <a href="/{$snake}" class="text-gray-600 hover:underline">Back to list</a>
        <a href="/{$snake}/{{ item.id }}/edit" class="text-yellow-600 hover:underline">Edit</a>
    </div>
</div>
{% endblock %}
VOLT,
            'create' => <<<VOLT
{% extends 'layouts/app.volt' %}

{% block content %}
<div class="max-w-3xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-4">Create {$studly}</h1>

    <form method="POST" action="/{$snake}" class="bg-white shadow rounded-lg p-6">
VOLT . $this->getFormFields($fields) . <<<VOLT
        <div class="flex gap-2 mt-6">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Save</button>
            <a href="/{$snake}" class="text-gray-600 hover:underline px-4 py-2">Cancel</a>
        </div>
    </form>
</div>
{% endblock %}
VOLT,
            'edit' => <<<VOLT
{% extends 'layouts/app.volt' %}

{% block content %}
<div class="max-w-3xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-4">Edit {$studly}</h1>

    <form method="POST" action="/{$snake}/{{ item.id }}" class="bg-white shadow rounded-lg p-6">
VOLT . $this->getFormFields($fields, true) . <<<VOLT
        <div class="flex gap-2 mt-6">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
            <a href="/{$snake}" class="text-gray-600 hover:underline px-4 py-2">Cancel</a>
        </div>
    </form>
</div>
{% endblock %}
VOLT,
        };
    }

    private function getTableHeaders(array $fields): string
    {
        $headers = '';
        foreach ($fields as $field => $type) {
            $headers .= "                    <th class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase\">" . ucfirst($field) . "</th>\n";
        }
        return $headers;
    }

    private function getTableCells(array $fields, string $snake): string
    {
        $cells = '';
        foreach ($fields as $field => $type) {
            $cells .= "                    <td class=\"px-6 py-4 whitespace-nowrap\">{{ item.{$field} }}</td>\n";
        }
        return $cells;
    }

    private function getDetailFields(array $fields): string
    {
        $html = '';
        foreach ($fields as $field => $type) {
            $html .= "        <div class=\"mb-4\">\n";
            $html .= "            <label class=\"block text-sm font-medium text-gray-700\">" . ucfirst($field) . "</label>\n";
            $html .= "            <p class=\"mt-1 text-gray-900\">{{ item.{$field} }}</p>\n";
            $html .= "        </div>\n";
        }
        return $html;
    }

    private function getFormFields(array $fields, bool $edit = false): string
    {
        $html = '';
        foreach ($fields as $field => $type) {
            $value = $edit ? " value=\"{{ item.{$field} }}\"" : '';
            $html .= "        <div class=\"mb-4\">\n";
            $html .= "            <label class=\"block text-sm font-medium text-gray-700\">" . ucfirst($field) . "</label>\n";

            if ($type === 'text') {
                $textareaValue = $edit ? "item.{$field}" : '';
                $html .= "            <textarea name=\"{$field}\" rows=\"4\" class=\"mt-1 block w-full rounded-md border-gray-300 shadow-sm\">{{ {$textareaValue} }}</textarea>\n";
            } else {
                $html .= "            <input type=\"text\" name=\"{$field}\"{$value} class=\"mt-1 block w-full rounded-md border-gray-300 shadow-sm\">\n";
            }

            $html .= "        </div>\n";
        }
        return $html;
    }

    private function studly(string $value): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $value)));
    }

    private function snake(string $value): string
    {
        return strtolower(preg_replace('/[A-Z]/', '_$0', lcfirst($value)));
    }

    private function plural(string $value): string
    {
        if (str_ends_with($value, 'y') && !in_array(substr($value, -2), ['ay', 'ey', 'oy', 'uy'])) {
            return substr($value, 0, -1) . 'ies';
        }
        if (str_ends_with($value, 's') || str_ends_with($value, 'x') || str_ends_with($value, 'z') || str_ends_with($value, 'ch') || str_ends_with($value, 'sh')) {
            return $value . 'es';
        }
        return $value . 's';
    }

    private function writeFile(string $path, string $content): void
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($path, $content);
    }

    private function ensureDir(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
