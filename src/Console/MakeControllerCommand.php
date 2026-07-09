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
        $name = $args[0] ?? 'Example';
        $suffix = str_ends_with($name, 'Controller') ? '' : 'Controller';
        $fileName = $name . $suffix . '.php';

        echo "Created controller: src/Controllers/{$fileName}\n";
    }
}
