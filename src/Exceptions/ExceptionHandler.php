<?php

declare(strict_types=1);

namespace Tavp\Core\Exceptions;

use Tavp\Core\Http\Response;

/**
 * Catches exceptions thrown during request handling and renders a
 * consistent response — HTML for browsers, JSON for APIs.
 */
class ExceptionHandler
{
    public function __construct(
        private bool $debug = false,
    ) {
    }

    /**
     * Render an exception into a Response.
     */
    public function render(\Throwable $exception): Response
    {
        $status = $this->statusFor($exception);
        $isApi = $this->requestWantsJson();

        if ($isApi) {
            return (new Response())->json([
                'success' => false,
                'error' => [
                    'code' => $status,
                    'message' => $exception->getMessage() ?: 'Server error',
                    'errors' => [],
                ],
            ], $status);
        }

        $message = $this->debug
            ? $exception->getMessage() . "\n" . $exception->getTraceAsString()
            : 'Something went wrong.';

        return (new Response())
            ->setStatusCode($status)
            ->setContent('<h1>' . $status . '</h1><pre>' . htmlspecialchars($message) . '</pre>');
    }

    private function statusFor(\Throwable $exception): int
    {
        if ($exception instanceof NotFoundException) {
            return 404;
        }
        if ($exception instanceof UnauthorizedException) {
            return 401;
        }
        if ($exception instanceof ForbiddenException) {
            return 403;
        }
        if ($exception instanceof ValidationException) {
            return 422;
        }

        return 500;
    }

    private function requestWantsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';

        return str_contains($accept, 'application/json');
    }
}
