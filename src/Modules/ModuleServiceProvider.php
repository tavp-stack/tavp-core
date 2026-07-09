<?php

declare(strict_types=1);

namespace Tavp\Core\Modules;

/**
 * Base class every module's service provider extends.
 *
 * register() binds services into the container; boot() runs after all
 * modules are registered (routes, migrations, views).
 */
abstract class ModuleServiceProvider
{
    /**
     * Register services, routes, migrations and views for the module.
     */
    abstract public function register(): void;

    /**
     * Boot the module once the framework is ready.
     */
    public function boot(): void
    {
    }
}
