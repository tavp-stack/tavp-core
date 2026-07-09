<?php

declare(strict_types=1);

namespace Tavp\Core\Templates;

/**
 * The Enterprise tier: application + API + JWT + Docker + deploy configs.
 */
class EnterpriseTemplate extends TierTemplate
{
    public function name(): string
    {
        return 'enterprise';
    }

    public function description(): string
    {
        return 'A full app with API, JWT, Docker and deployment configs.';
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
        return array_merge(
            (new ApplicationTemplate())->files(),
            ['.lando.yml', 'docker-compose.yml', '.tavp-deploy.yml']
        );
    }
}
