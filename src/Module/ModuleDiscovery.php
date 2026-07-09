<?php

declare(strict_types=1);

namespace Tavp\Module;

use Composer\InstalledVersions;

/**
 * Composer module discovery — find and load tavp modules.
 */
class ModuleDiscovery
{
    /**
     * Discover all installed TAVP modules.
     */
    public static function discover(): array
    {
        $modules = [];
        $installed = InstalledVersions::getInstalledPackages();

        foreach ($installed as $package) {
            if (str_starts_with($package, 'tavp/module-') || str_starts_with($package, 'tavp/')) {
                $modulePath = InstalledVersions::getInstallPath($package);
                $providerClass = self::findProvider($modulePath);

                if ($providerClass) {
                    $modules[$package] = [
                        'path' => $modulePath,
                        'provider' => $providerClass,
                    ];
                }
            }
        }

        return $modules;
    }

    /**
     * Find the service provider class for a module.
     */
    private static function findProvider(string $modulePath): ?string
    {
        // Check for common provider locations
        $possiblePaths = [
            $modulePath . '/src/ModuleServiceProvider.php',
            $modulePath . '/src/ServiceProvider.php',
            $modulePath . '/ModuleServiceProvider.php',
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $content = file_get_contents($path);
                if (preg_match('/namespace\s+([^;]+)/', $content, $nsMatch) &&
                    preg_match('/class\s+(\w+)/', $content, $classMatch)) {
                    return $nsMatch[1] . '\\' . $classMatch[1];
                }
            }
        }

        return null;
    }
}
