<?php

declare(strict_types=1);

namespace Tavp\Core\Http\Middleware;

use Tavp\Core\Http\Request;
use Tavp\Core\Http\Response;

/**
 * Starts and manages the session for every request.
 */
class SessionMiddleware implements Middleware
{
    public function handle(callable $next): mixed
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $next();
    }
}
