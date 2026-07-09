<?php

declare(strict_types=1);

namespace Tavp\Core\Console;

/**
 * tavp make:controller — generate a controller class.
 *
 * Usage: tavp make:controller <Name> [--api] [--resource]
 */
class MakeControllerCommand
{
    public function handle(array $args): void
    {
        $name = $args[0] ?? null;

        if ($name === null) {
            echo "Usage: tavp make:controller <Name>\n";
            return;
        }

        $isApi = in_array('--api', $args, true);
        $isResource = in_array('--resource', $args, true);

        $this->createController($name, $isApi, $isResource);

        echo "Controller created successfully.\n";
    }

    private function createController(string $name, bool $api, bool $resource): void
    {
        $suffix = str_ends_with($name, 'Controller') ? '' : 'Controller';
        $className = $name . $suffix;
        $fileName = $className . '.php';

        $baseClass = $api ? 'ApiController' : 'BaseController';
        $namespace = $api ? 'Controllers\Api' : 'Controllers';

        $methods = $resource ? $this->resourceMethods($className) : $this->stubMethods();

        $content = <<<PHP
<?php

declare(strict_types=1);

namespace Tavp\Core\{$namespace};

use Tavp\Core\Controllers\{$baseClass};

class {$className} extends {$baseClass}
{
{$methods}
}

PHP;

        $path = base_path("src/Controllers/{$fileName}");

        if ($api) {
            $path = base_path("src/Controllers/Api/{$fileName}");
        }

        if (file_exists($path)) {
            echo "  Controller {$fileName} already exists. Skipping.\n";
            return;
        }

        file_put_contents($path, $content);
        $displayPath = $api ? "src/Controllers/Api/{$fileName}" : "src/Controllers/{$fileName}";
        echo "  Created {$displayPath}\n";
    }

    private function stubMethods(): string
    {
        return <<<'PHP'
    public function index(): string
    {
        return $this->json(['data' => []]);
    }

    public function show(int $id): string
    {
        return $this->json(['data' => ['id' => $id]]);
    }
PHP;
    }

    private function resourceMethods(string $className): string
    {
        return <<<'PHP'
    public function index(): string
    {
        return $this->json(['data' => []]);
    }

    public function create(): string
    {
        return $this->view('resource.create');
    }

    public function store(): string
    {
        return $this->json(['message' => 'Created'], 201);
    }

    public function show(int $id): string
    {
        return $this->json(['data' => ['id' => $id]]);
    }

    public function edit(int $id): string
    {
        return $this->view('resource.edit', ['id' => $id]);
    }

    public function update(int $id): string
    {
        return $this->json(['message' => 'Updated']);
    }

    public function destroy(int $id): string
    {
        return $this->json(['message' => 'Deleted'], 204);
    }
PHP;
    }
}
