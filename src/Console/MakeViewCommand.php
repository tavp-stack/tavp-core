<?php

declare(strict_types=1);

namespace Tavp\Core\Console;

/**
 * tavp make:view — generate a view template.
 *
 * Usage: tavp make:view <Name> [--layout=name]
 */
class MakeViewCommand
{
    public function handle(array $args): void
    {
        $name = $args[0] ?? null;

        if ($name === null) {
            echo "Usage: tavp make:view <Name>\n";
            return;
        }

        $layout = $this->extractOption($args, '--layout', 'layouts.app');

        $fileName = $this->toSnake($name) . '.volt';
        $templateName = str_replace('.', '/', $layout);

        $content = <<<VOLT
{% extends '{$templateName}' %}

{% block content %}
<div class="px-4 py-6 sm:px-0">
    <h1 class="text-2xl font-bold text-gray-900">{$name}</h1>
    <p class="mt-2 text-gray-600">This is the {$name} view.</p>
</div>
{% endblock %}

VOLT;

        $dir = base_path('resources/views/' . str_replace('.', '/', $name));

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $path = $dir . '/' . $fileName;

        if (file_exists($path)) {
            echo "  View {$fileName} already exists. Skipping.\n";
            return;
        }

        file_put_contents($path, $content);
        echo "  Created resources/views/{$name}/{$fileName}\n";
    }

    private function extractOption(array $args, string $key, string $default): string
    {
        foreach ($args as $arg) {
            if (str_starts_with($arg, $key . '=')) {
                return substr($arg, strlen($key) + 1);
            }
        }

        return $default;
    }

    private function toSnake(string $value): string
    {
        $result = preg_replace('/(?<!^)[A-Z]/', '_$0', $value);

        return strtolower((string) $result);
    }
}
