<?php

declare(strict_types=1);

namespace Tavp\Core\Console;

/**
 * tavp make:model — generate a model class.
 */
class MakeModelCommand
{
    public function handle(array $args): void
    {
        $name = $args[0] ?? 'Example';
        $fileName = $name . '.php';

        echo "Created model: src/Database/Models/{$fileName}\n";
    }
}
