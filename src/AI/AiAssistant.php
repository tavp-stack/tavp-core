<?php

declare(strict_types=1);

namespace Tavp\AI;

/**
 * AI Assistant — chatbot for admin panel.
 */
class AiAssistant
{
    public function __construct(private AiManager $ai)
    {
    }

    /**
     * Process a chat message.
     */
    public function chat(string $message, array $history = []): string
    {
        $messages = array_merge(
            [['role' => 'system', 'content' => $this->getSystemPrompt()]],
            $history,
            [['role' => 'user', 'content' => $message]]
        );

        return $this->ai->chat($messages);
    }

    /**
     * Generate a page from description.
     */
    public function generatePage(string $description): string
    {
        return $this->ai->generateCode(
            "Generate a complete Volt template page for: {$description}",
            'volt'
        );
    }

    /**
     * Generate a section.
     */
    public function generateSection(string $description): string
    {
        return $this->ai->generateCode(
            "Generate a Volt template section for: {$description}",
            'volt'
        );
    }

    private function getSystemPrompt(): string
    {
        return 'You are TAVP AI Assistant. You help developers build web applications using TAVP stack (Phalcon + Volt + Tailwind + Alpine.js). You can generate Volt templates, PHP code, and help with configuration. Be concise and helpful.';
    }
}
