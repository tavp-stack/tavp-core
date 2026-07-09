<?php

declare(strict_types=1);

namespace Tavp\Core\Exceptions;

/**
 * Thrown when a user lacks permission for a resource (HTTP 403).
 */
class ForbiddenException extends \RuntimeException
{
}
