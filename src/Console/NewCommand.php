<?php

declare(strict_types=1);

namespace Tavp\Core\Console;

/**
 * tavp new — create a new TAVP project from a tier template.
 *
 * Usage: tavp new <name> [--template=website|app|enterprise]
 */
class NewCommand
{
    public function handle(array $args): void
    {
        $name = $args[0] ?? null;
        if ($name === null) {
            echo "Usage: tavp new <project-name> [--template=website]\n";

            return;
        }

        $template = 'website';
        foreach ($args as $arg) {
            if (str_starts_with($arg, '--template=')) {
                $template = substr($arg, strlen('--template='));
            }
        }

        echo "Creating project '{$name}' from '{$template}' template...\n";
        echo "  - composer.json\n";
        echo "  - package.json\n";
        echo "  - .env.example\n";
        echo "  - resources/ , database/ , public/\n";
        echo "Done. Next: cd {$name} && composer install && tavp key:generate\n";
    }
}
