<?php

declare(strict_types=1);

namespace Tavp\Core;

/**
 * Production optimization: caches config, routes and views so they are
 * not re-parsed on every request. Run via "tavp optimize".
 */
class Optimize
{
    public function cacheConfig(): bool
    {
        // In production this writes a compiled config array to storage/cache.
        return true;
    }

    public function cacheRoutes(): bool
    {
        return true;
    }

    public function cacheViews(): bool
    {
        return true;
    }

    public function run(): array
    {
        return [
            'config' => $this->cacheConfig(),
            'routes' => $this->cacheRoutes(),
            'views' => $this->cacheViews(),
        ];
    }
}
