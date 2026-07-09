<?php

declare(strict_types=1);

namespace Tavp\Core\Exceptions;

/**
 * Thrown when a requested resource cannot be found (HTTP 404).
 */
class NotFoundException extends \RuntimeException
{
}
