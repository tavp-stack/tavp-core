<?php

declare(strict_types=1);

namespace Tavp\Core\AI;

/**
 * Null AI driver (fallback when no provider configured).
 */
class NullAiDriver implements AiDriver
{
    public function complete(string $prompt, array $options = []): string
    {
        return 'AI provider not configured. Please configure an AI driver.';
    }

    public function chat(array $messages, array $options = []): string
    {
        return 'AI provider not configured. Please configure an AI driver.';
    }

    public function generateCode(string $description, string $language): string
    {
        return '// AI provider not configured';
    }

    public function generateContent(string $type, string $topic, array $options = []): string
    {
        return 'AI provider not configured.';
    }
}
