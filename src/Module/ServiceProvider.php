<?php

declare(strict_types=1);

namespace Tavp\Core\Module;

/**
 * Module service provider — register and boot modules.
 */
interface ServiceProvider
{
    /**
     * Register services with the container.
     */
    public function register(): void;

    /**
     * Boot services after all providers are registered.
     */
    public function boot(): void;

    /**
     * Load module routes.
     */
    public function loadRoutes(): void;

    /**
     * Load module migrations.
     */
    public function loadMigrations(): void;

    /**
     * Load module views.
     */
    public function loadViews(): void;
}
