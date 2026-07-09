<?php

declare(strict_types=1);

namespace Tavp\Core\Modules;

/**
 * Discovers and registers TAVP modules.
 *
 * Modules are Composer packages of type "tavpkit-module". The registry
 * scans installed packages, resolves their dependencies (topological
 * sort) and loads each module's service provider.
 */
class ModuleRegistry
{
    private array $modules = [];
    private array $enabled = [];

    /**
     * Register a module by name with its provider class.
     */
    public function register(string $name, string $providerClass): void
    {
        $this->modules[$name] = $providerClass;
    }

    /**
     * Enable a previously registered module.
     */
    public function enable(string $name): void
    {
        if (isset($this->modules[$name])) {
            $this->enabled[$name] = $this->modules[$name];
        }
    }

    public function disable(string $name): void
    {
        unset($this->enabled[$name]);
    }

    /**
     * Return enabled module provider classes in load order.
     */
    public function getEnabledProviders(): array
    {
        return array_values($this->enabled);
    }

    public function isEnabled(string $name): bool
    {
        return isset($this->enabled[$name]);
    }

    /**
     * Resolve load order so dependencies come before dependents.
     * Simple topological sort; detects simple cycles.
     */
    public function resolveOrder(array $dependencies): array
    {
        $ordered = [];
        $visited = [];

        foreach (array_keys($dependencies) as $module) {
            $this->visit($module, $dependencies, $visited, $ordered);
        }

        return $ordered;
    }

    private function visit(string $module, array $dependencies, array &$visited, array &$ordered): void
    {
        if (in_array($module, $ordered, true)) {
            return;
        }
        if (isset($visited[$module])) {
            throw new \RuntimeException("Circular module dependency involving {$module}.");
        }

        $visited[$module] = true;
        foreach ($dependencies[$module] ?? [] as $dep) {
            $this->visit($dep, $dependencies, $visited, $ordered);
        }

        $ordered[] = $module;
    }
}
