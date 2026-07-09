<?php

declare(strict_types=1);

namespace Tavp\AI;

/**
 * AI Manager — multi-driver AI integration.
 */
class AiManager
{
    private array $drivers = [];
    private string $defaultDriver;

    public function __construct(array $config)
    {
        $this->defaultDriver = $config['default'] ?? 'ollama';

        foreach ($config['providers'] ?? [] as $name => $providerConfig) {
            $this->drivers[$name] = match ($providerConfig['driver'] ?? 'ollama') {
                'openai' => new OpenAiDriver($providerConfig),
                'anthropic' => new AnthropicDriver($providerConfig),
                'ollama' => new OllamaDriver($providerConfig),
                'tavp-cloud' => new TavpCloudAiDriver($providerConfig),
                default => null,
            };
        }
    }

    /**
     * Generate text completion.
     */
    public function complete(string $prompt, array $options = []): string
    {
        return $this->driver()->complete($prompt, $options);
    }

    /**
     * Generate chat completion.
     */
    public function chat(array $messages, array $options = []): string
    {
        return $this->driver()->chat($messages, $options);
    }

    /**
     * Generate code.
     */
    public function generateCode(string $description, string $language = 'php'): string
    {
        return $this->driver()->generateCode($description, $language);
    }

    /**
     * Generate content.
     */
    public function generateContent(string $type, string $topic, array $options = []): string
    {
        return $this->driver()->generateContent($type, $topic, $options);
    }

    /**
     * Get available drivers.
     */
    public function getDrivers(): array
    {
        return array_keys(array_filter($this->drivers));
    }

    private function driver(?string $name = null): AiDriver
    {
        $driver = $name ?? $this->defaultDriver;
        return $this->drivers[$driver] ?? $this->drivers[$this->defaultDriver] ?? new NullAiDriver();
    }
}

interface AiDriver
{
    public function complete(string $prompt, array $options = []): string;
    public function chat(array $messages, array $options = []): string;
    public function generateCode(string $description, string $language): string;
    public function generateContent(string $type, string $topic, array $options = []): string;
}
