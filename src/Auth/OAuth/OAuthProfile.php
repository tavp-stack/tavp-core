<?php

declare(strict_types=1);

namespace Tavp\Core\Auth\OAuth;

/**
 * Normalized user profile returned by any OAuth provider.
 */
class OAuthProfile
{
    public function __construct(
        public string $provider,
        public string $providerId,
        public string $email,
        public string $name,
    ) {
    }
}
