<?php

declare(strict_types=1);

namespace Tavp\Kit;

/**
 * User profile management.
 */
class ProfileService
{
    /**
     * Get user profile.
     */
    public function getProfile(int $userId): ?array
    {
        // Implementation: fetch from user_profiles table
        return null;
    }

    /**
     * Update user profile.
     */
    public function updateProfile(int $userId, array $data): bool
    {
        // Implementation: update user_profiles table
        return true;
    }

    /**
     * Upload avatar.
     */
    public function uploadAvatar(int $userId, array $file): string
    {
        // Implementation: save file, return URL
        return '/avatars/default.png';
    }

    /**
     * Get user settings.
     */
    public function getSettings(int $userId): array
    {
        // Implementation: fetch from user_settings table
        return [
            'theme' => 'light',
            'language' => 'en',
            'timezone' => 'UTC',
            'email_notifications' => true,
        ];
    }

    /**
     * Update user settings.
     */
    public function updateSettings(int $userId, array $settings): bool
    {
        // Implementation: update user_settings table
        return true;
    }
}
