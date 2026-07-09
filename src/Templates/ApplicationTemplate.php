<?php

declare(strict_types=1);

namespace Tavp\Core\Templates;

/**
 * The Application tier: website + TAVPid OTP auth + database.
 */
class ApplicationTemplate extends TierTemplate
{
    public function name(): string
    {
        return 'application';
    }

    public function description(): string
    {
        return 'A web app with OTP login (TAVPid) and a database.';
    }

    public function usesDatabase(): bool
    {
        return true;
    }

    public function usesAuth(): bool
    {
        return true;
    }

    public function files(): array
    {
        return [
            'composer.json',
            'package.json',
            '.env.example',
            'routes/web.php',
            'routes/api.php',
            'database/migrations/0001_create_users_table.php',
            'resources/views/auth/login.volt',
        ];
    }
}
