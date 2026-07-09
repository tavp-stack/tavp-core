<?php

declare(strict_types=1);

namespace Tavp\Core\Console\Commands;

/**
 * tavp weave:enable — enable PHP Fibers support (TAVP Weave)
 */
class WeaveEnableCommand
{
    public function execute(array $arguments): void
    {
        echo "Enabling TAVP Weave (PHP Fibers)...\n\n";

        // 1. Check PHP version
        $phpVersion = PHP_VERSION;
        if (version_compare($phpVersion, '8.1.0', '<')) {
            echo "✗ PHP {$phpVersion} detected. TAVP Weave requires PHP 8.1+\n";
            echo "  Please upgrade PHP to version 8.1 or higher.\n";
            exit(1);
        }
        echo "  ✓ PHP {$phpVersion} (8.1+ required)\n";

        // 2. Check Fiber class exists
        if (!class_exists('Fiber')) {
            echo "✗ Fiber class not available. Ensure PHP 8.1+ is properly installed.\n";
            exit(1);
        }
        echo "  ✓ Fiber class available\n";

        // 3. Generate weave config
        $configPath = base_path('config/weave.php');
        $config = $this->generateConfig();
        file_put_contents($configPath, $config);
        echo "  ✓ Config written: config/weave.php\n";

        // 4. Check for Swoole conflict
        if (extension_loaded('swoole')) {
            echo "  ⚠ Swoole extension detected. TAVP Weave is for PHP-FPM environments.\n";
            echo "    If you want Swoole coroutines, use: tavp coil:start\n";
        }

        echo "\n✓ TAVP Weave enabled!\n\n";
        echo "Usage:\n";
        echo "  // Use Fiber for parallel operations\n";
        echo "  \$fiber = new Fiber(function () {\n";
        echo "      \$result = Fiber::suspend('paused');\n";
        echo "      echo \"Resumed with: \" . \$result;\n";
        echo "  });\n";
        echo "  \$fiber->start();\n";
        echo "  \$fiber->resume('hello');\n";
    }

    private function generateConfig(): string
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | TAVP Weave Configuration
    |--------------------------------------------------------------------------
    |
    | TAVP Weave uses PHP Fibers for lightweight async operations.
    | Works on shared hosting with PHP-FPM (no extensions needed).
    |
    */

    'enabled' => true,

    'timeout' => 30,

    'max_concurrent' => 10,
];
PHP;
    }
}
