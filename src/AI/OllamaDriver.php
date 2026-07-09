<?php

declare(strict_types=1);

namespace Tavp\AI;

/**
 * Ollama driver (self-hosted, free).
 */
class OllamaDriver implements AiDriver
{
    private string $host;
    private string $model;

    public function __construct(array $config)
    {
        $this->host = $config['host'] ?? 'http://localhost:11434';
        $this->model = $config['model'] ?? 'llama3';
    }

    public function complete(string $prompt, array $options = []): string
    {
        return $this->api('/api/generate', [
            'model' => $this->model,
            'prompt' => $prompt,
            'stream' => false,
        ]);
    }

    public function chat(array $messages, array $options = []): string
    {
        return $this->api('/api/chat', [
            'model' => $this->model,
            'messages' => $messages,
            'stream' => false,
        ]);
    }

    public function generateCode(string $description, string $language): string
    {
        $prompt = "Generate {$language} code for: {$description}\n\nReturn only the code without explanation.";
        return $this->complete($prompt);
    }

    public function generateContent(string $type, string $topic, array $options = []): string
    {
        $prompt = "Generate {$type} content about: {$topic}";
        return $this->complete($prompt, $options);
    }

    private function api(string $endpoint, array $data): string
    {
        $url = $this->host . $endpoint;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($data),
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        return $result['response'] ?? $result['message']['content'] ?? '';
    }
}
