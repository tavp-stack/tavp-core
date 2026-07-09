<?php

declare(strict_types=1);

namespace Tavp\Core\Exceptions;

/**
 * Thrown when a user is not authenticated (HTTP 401).
 */
class UnauthorizedException extends \RuntimeException
{
}
