<?php

declare(strict_types=1);

namespace Tavp\Core\Http\Middleware;

use Tavp\Core\Http\Request;
use Tavp\Core\Http\Response;

/**
 * Redirects authenticated users away from login/register pages.
 */
class RedirectIfAuthenticated implements Middleware
{
    public function handle(callable $next): mixed
    {
        $token = $_COOKIE['tavp_token'] ?? $_SESSION['auth_token'] ?? null;

        if ($token !== null) {
            /** @var \Tavp\Core\Auth\TokenService $tokenService */
            $tokenService = app('tokens');
            $payload = $tokenService->decode($token);

            if ($payload !== null && ($payload['type'] ?? '') === 'access') {
                $accept = $_SERVER['HTTP_ACCEPT'] ?? '';

                if (str_contains($accept, 'application/json')) {
                    return (new Response())->json([
                        'success' => true,
                        'message' => 'Already authenticated.',
                    ]);
                }

                return (new Response())
                    ->header('Location', '/dashboard')
                    ->setStatusCode(302);
            }
        }

        return $next();
    }
}
