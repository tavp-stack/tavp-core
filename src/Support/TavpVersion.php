<?php

declare(strict_types=1);

namespace Tavp\Core\Support;

/**
 * TAVP Stack Version
 */
class TavpVersion
{
    public const VERSION = '1.0.0';
    public const MAJOR = 1;
    public const MINOR = 0;
    public const PATCH = 0;
    public const CODENAME = 'Stable';

    /**
     * Get full version string.
     */
    public static function get(): string
    {
        return self::VERSION;
    }

    /**
     * Get version as array.
     */
    public static function toArray(): array
    {
        return [
            'major' => self::MAJOR,
            'minor' => self::MINOR,
            'patch' => self::PATCH,
            'codename' => self::CODENAME,
        ];
    }

    /**
     * Check if this is a stable release.
     */
    public static function isStable(): bool
    {
        return self::MAJOR >= 1;
    }

    /**
     * Get PHP requirement.
     */
    public static function getPhpRequirement(): string
    {
        return '>=8.3';
    }

    /**
     * Get Phalcon requirement.
     */
    public static function getPhalconRequirement(): string
    {
        return '>=5.16';
    }
}
