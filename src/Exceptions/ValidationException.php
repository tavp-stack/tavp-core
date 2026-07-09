<?php

declare(strict_types=1);

namespace Tavp\Core\Exceptions;

/**
 * Thrown when request validation fails (HTTP 422).
 */
class ValidationException extends \RuntimeException
{
    public function __construct(
        private array $errors = [],
        string $message = 'Validation failed',
    ) {
        parent::__construct($message);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
