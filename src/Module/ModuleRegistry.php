<?php

declare(strict_types=1);

namespace Tavp\Core\Module;

/**
 * Module registry — discover, register, and manage modules.
 */
class ModuleRegistry
{
    private array $modules = [];
    private array $registered = [];
    private array $booted = [];

    /**
     * Register a module.
     */
    public function register(string $name, ServiceProvider $provider): void
    {
        $this->modules[$name] = $provider;
    }

    /**
     * Boot all registered modules.
     */
    public function bootAll(): void
    {
        $order = $this->resolveBootOrder();

        foreach ($order as $name) {
            if (isset($this->modules[$name]) && !in_array($name, $this->booted)) {
                $this->modules[$name]->boot();
                $this->booted[] = $name;
            }
        }
    }

    /**
     * Load all module routes.
     */
    public function loadAllRoutes(): void
    {
        foreach ($this->modules as $provider) {
            $provider->loadRoutes();
        }
    }

    /**
     * Load all module migrations.
     */
    public function loadAllMigrations(): void
    {
        foreach ($this->modules as $provider) {
            $provider->loadMigrations();
        }
    }

    /**
     * Load all module views.
     */
    public function loadAllViews(): void
    {
        foreach ($this->modules as $provider) {
            $provider->loadViews();
        }
    }

    /**
     * Get a module by name.
     */
    public function getModule(string $name): ?ServiceProvider
    {
        return $this->modules[$name] ?? null;
    }

    /**
     * Check if a module is registered.
     */
    public function hasModule(string $name): bool
    {
        return isset($this->modules[$name]);
    }

    /**
     * Get all registered module names.
     */
    public function getModuleNames(): array
    {
        return array_keys($this->modules);
    }

    /**
     * Resolve boot order based on dependencies.
     */
    private function resolveBootOrder(): array
    {
        // Simple topological sort
        $visited = [];
        $order = [];

        foreach ($this->modules as $name => $provider) {
            if (!in_array($name, $visited)) {
                $this->visitModule($name, $visited, $order);
            }
        }

        return $order;
    }

    private function visitModule(string $name, array &$visited, array &$order): void
    {
        $visited[] = $name;

        // Check dependencies (simplified)
        if (isset($this->modules[$name]) && method_exists($this->modules[$name], 'dependencies')) {
            $dependencies = $this->modules[$name]->dependencies();
            foreach ($dependencies as $dep) {
                if (!in_array($dep, $visited) && isset($this->modules[$dep])) {
                    $this->visitModule($dep, $visited, $order);
                }
            }
        }

        $order[] = $name;
    }
}
