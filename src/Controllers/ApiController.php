<?php

declare(strict_types=1);

namespace Tavp\Core\Controllers;

use Tavp\Core\Http\Response;

/**
 * Base controller for JSON APIs.
 *
 * Standardises the success/error response shape so every API endpoint
 * in TAVP looks the same to consumers.
 */
abstract class ApiController extends BaseController
{
    /**
     * Standard success envelope.
     */
    protected function success(mixed $data, int $status = 200): Response
    {
        return $this->json([
            'success' => true,
            'data' => $data,
        ], $status);
    }

    /**
     * Standard error envelope.
     */
    protected function error(string $message, int $status = 400, array $errors = []): Response
    {
        return $this->json([
            'success' => false,
            'error' => [
                'code' => $status,
                'message' => $message,
                'errors' => $errors,
            ],
        ], $status);
    }
}
