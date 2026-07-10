<?php

declare(strict_types=1);

namespace Tavp\Core\AI;

/**
 * Anthropic driver (Claude).
 */
class AnthropicDriver implements AiDriver
{
    private string $apiKey;
    private string $model;

    public function __construct(array $config)
    {
        $this->apiKey = $config['api_key'] ?? '';
        $this->model = $config['model'] ?? 'claude-3-opus';
    }

    public function complete(string $prompt, array $options = []): string
    {
        return $this->api('messages', [
            'model' => $this->model,
            'max_tokens' => $options['max_tokens'] ?? 2048,
            'messages' => [['role' => 'user', 'content' => $prompt]],
        ]);
    }

    public function chat(array $messages, array $options = []): string
    {
        return $this->api('messages', [
            'model' => $this->model,
            'max_tokens' => $options['max_tokens'] ?? 2048,
            'messages' => $messages,
        ]);
    }

    public function generateCode(string $description, string $language): string
    {
        $prompt = "Generate {$language} code for: {$description}\n\nReturn only the code without explanation.";
        return $this->complete($prompt, ['temperature' => 0.3]);
    }

    public function generateContent(string $type, string $topic, array $options = []): string
    {
        $prompt = "Generate {$type} content about: {$topic}";
        return $this->complete($prompt, $options);
    }

    private function api(string $endpoint, array $data): string
    {
        $url = "https://api.anthropic.com/v1/{$endpoint}";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'x-api-key: ' . $this->apiKey,
                'anthropic-version: 2023-06-01',
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        return $result['content'][0]['text'] ?? '';
    }
}
