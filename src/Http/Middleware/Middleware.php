<?php

declare(strict_types=1);

namespace Tavp\Core\Http\Middleware;

/**
 * Contract every middleware must follow.
 *
 * handle() receives the request and a "next" callback. It may short-circuit
 * (return a Response) or pass control onward by calling $next().
 */
interface Middleware
{
    public function handle(callable $next): mixed;
}
