<?php

declare(strict_types=1);

namespace Tavp\Core\Auth\OAuth;

/**
 * Social OAuth login (Google, Apple, GitHub).
 *
 * Each provider returns a normalized user profile (email + name) which
 * the AuthService links to a local user via the social_accounts table.
 */
abstract class OAuthProvider
{
    abstract public function name(): string;

    /**
     * Build the authorization redirect URL.
     */
    abstract public function authorizationUrl(): string;

    /**
     * Exchange a code for a normalized profile.
     */
    abstract public function userFromCode(string $code): OAuthProfile;

    /**
     * Return the social_accounts identifier for this provider.
     */
    public function providerKey(): string
    {
        return $this->name();
    }
}
