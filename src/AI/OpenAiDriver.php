<?php

declare(strict_types=1);

namespace Tavp\Core\AI;

/**
 * OpenAI driver (GPT-4, GPT-3.5).
 */
class OpenAiDriver implements AiDriver
{
    private string $apiKey;
    private string $model;

    public function __construct(array $config)
    {
        $this->apiKey = $config['api_key'] ?? '';
        $this->model = $config['model'] ?? 'gpt-4';
    }

    public function complete(string $prompt, array $options = []): string
    {
        return $this->api('completions', [
            'model' => $this->model,
            'prompt' => $prompt,
            'max_tokens' => $options['max_tokens'] ?? 2048,
            'temperature' => $options['temperature'] ?? 0.7,
        ]);
    }

    public function chat(array $messages, array $options = []): string
    {
        return $this->api('chat/completions', [
            'model' => $this->model,
            'messages' => $messages,
            'max_tokens' => $options['max_tokens'] ?? 2048,
            'temperature' => $options['temperature'] ?? 0.7,
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
        $url = "https://api.openai.com/v1/{$endpoint}";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                "Authorization: Bearer {$this->apiKey}",
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        return $result['choices'][0]['message']['content'] ?? '';
    }
}
