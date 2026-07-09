<?php

declare(strict_types=1);

namespace Tavp\Core\Console\Commands;

/**
 * tavp optimize — production optimization (cache config, routes, views, autoloader).
 */
class OptimizeCommand
{
    public function execute(array $arguments): void
    {
        echo "Optimizing TAVP application...\n\n";

        $steps = [
            'Config' => 'cacheConfig',
            'Routes' => 'cacheRoutes',
            'Views' => 'cacheViews',
            'Autoloader' => 'optimizeAutoloader',
        ];

        foreach ($steps as $name => $method) {
            echo "  Caching {$name}...";
            try {
                $this->{$method}();
                echo " ✓\n";
            } catch (\Throwable $e) {
                echo " ✗ {$e->getMessage()}\n";
            }
        }

        echo "\n✓ Application optimized!\n";
    }

    private function cacheConfig(): void
    {
        $configDir = base_path('config');
        $cacheDir = storage_path('cache/config');

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        $config = [];
        $files = glob($configDir . '/*.php');

        foreach ($files as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            $config[$name] = require $file;
        }

        $cached = '<?php return ' . var_export($config, true) . ';';
        file_put_contents($cacheDir . '/app.php', $cached);
    }

    private function cacheRoutes(): void
    {
        $routesDir = base_path('routes');
        $cacheDir = storage_path('cache/routes');

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Collect all routes
        $routes = [];
        $files = glob($routesDir . '/*.php');

        foreach ($files as $file) {
            require $file;
        }

        $cached = '<?php return ' . var_export($routes, true) . ';';
        file_put_contents($cacheDir . '/app.php', $cached);
    }

    private function cacheViews(): void
    {
        // Volt compiles templates automatically
        // This just ensures compiled directory exists
        $compiledDir = storage_path('compiled/volt');

        if (!is_dir($compiledDir)) {
            mkdir($compiledDir, 0755, true);
        }
    }

    private function optimizeAutoloader(): void
    {
        $vendorDir = base_path('vendor');

        if (!is_dir($vendorDir)) {
            throw new \RuntimeException('Vendor directory not found. Run: composer install');
        }

        // Generate optimized autoloader
        exec('composer dump-autoload --optimize --no-dev 2>&1', $output, $exitCode);

        if ($exitCode !== 0) {
            throw new \RuntimeException('Failed to optimize autoloader: ' . implode("\n", $output));
        }
    }
}
