<?php

declare(strict_types=1);

namespace Tavp\Core\Console;

/**
 * tavp init — initialize a new git repository with .gitignore and first commit.
 *
 * Usage: tavp init
 */
class InitCommand
{
    public function handle(array $args): void
    {
        $dir = base_path();

        // Check if already a git repo
        if (is_dir($dir . '/.git')) {
            echo "Git repository already initialized.\n";
            return;
        }

        echo "Initializing git repository...\n";
        $this->run('git init');

        // Create .gitignore if not exists
        $gitignore = $dir . '/.gitignore';
        if (!is_file($gitignore)) {
            $content = <<<'GITIGNORE'
/vendor/
node_modules/
composer.lock
package-lock.json
.env
.env.*
!.env.example
/storage/cache/*
/storage/compiled/*
/storage/logs/*
!storage/cache/.gitkeep
!storage/compiled/.gitkeep
!storage/logs/.gitkeep
/public/build/
.DS_Store
Thumbs.db
*.log
GITIGNORE;

            file_put_contents($gitignore, $content);
            echo "Created .gitignore\n";
        }

        echo "Git repository initialized.\n";
        echo "Run: tavp push \"Initial commit\" to make first commit.\n";
    }

    private function run(string $command): string
    {
        $output = [];
        $exitCode = 0;

        exec($command . ' 2>&1', $output, $exitCode);

        return implode("\n", $output);
    }
}
