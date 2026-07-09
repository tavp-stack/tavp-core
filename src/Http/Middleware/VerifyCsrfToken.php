<?php

declare(strict_types=1);

namespace Tavp\Core\Http\Middleware;

use Tavp\Core\Http\Request;
use Tavp\Core\Http\Response;

/**
 * Blocks cross-site request forgery by checking a token on state-changing
 * requests (POST, PUT, PATCH, DELETE). GET requests pass through.
 */
class VerifyCsrfToken implements Middleware
{
    private array $safeMethods = ['GET', 'HEAD', 'OPTIONS'];

    public function handle(callable $next): mixed
    {
        $request = new Request();
        $method = $request->method();

        if (in_array($method, $this->safeMethods, true)) {
            return $next();
        }

        $token = $request->input('_token') ?? $this->tokenFromHeader();

        if (!is_string($token) || !$this->tokensMatch($token)) {
            return (new Response())
                ->setStatusCode(419)
                ->setContent('CSRF token mismatch.');
        }

        return $next();
    }

    private function tokenFromHeader(): ?string
    {
        return $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    }

    private function tokensMatch(string $token): bool
    {
        $expected = $_SESSION['_csrf_token'] ?? '';

        return is_string($expected) && hash_equals($expected, $token);
    }
}
