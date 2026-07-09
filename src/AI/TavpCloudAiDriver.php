<?php

declare(strict_types=1);

namespace Tavp\AI;

/**
 * TAVP Cloud AI driver (hosted, free tier available).
 */
class TavpCloudAiDriver implements AiDriver
{
    private string $apiKey;
    private string $host;

    public function __construct(array $config)
    {
        $this->apiKey = $config['api_key'] ?? '';
        $this->host = $config['host'] ?? 'https://ai.tavp.dev';
    }

    public function complete(string $prompt, array $options = []): string
    {
        return $this->api('/v1/completions', ['prompt' => $prompt] + $options);
    }

    public function chat(array $messages, array $options = []): string
    {
        return $this->api('/v1/chat', ['messages' => $messages] + $options);
    }

    public function generateCode(string $description, string $language): string
    {
        return $this->complete("Generate {$language} code for: {$description}");
    }

    public function generateContent(string $type, string $topic, array $options = []): string
    {
        return $this->complete("Generate {$type} content about: {$topic}", $options);
    }

    private function api(string $endpoint, array $data): string
    {
        $url = $this->host . $endpoint;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        return $result['content'] ?? $result['response'] ?? '';
    }
}
