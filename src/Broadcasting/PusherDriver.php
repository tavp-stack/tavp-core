<?php

declare(strict_types=1);

namespace Tavp\Core\Broadcasting;

/**
 * Pusher-compatible broadcast driver (Soketi/Pusher).
 */
class PusherDriver implements BroadcastDriver
{
    private string $appId;
    private string $appKey;
    private string $appSecret;
    private string $host;
    private int $port;

    public function __construct(array $config)
    {
        $this->appId = $config['app_id'] ?? '';
        $this->appKey = $config['app_key'] ?? '';
        $this->appSecret = $config['app_secret'] ?? '';
        $this->host = $config['host'] ?? 'ws://127.0.0.1:6001';
        $this->port = $config['port'] ?? 6001;
    }

    public function broadcast(string $channel, string $event, array $data): bool
    {
        $timestamp = time();
        $body = json_encode([
            'name' => $event,
            'channel' => $channel,
            'data' => json_encode($data),
        ]);

        $signature = hash_hmac('sha256', "{$this->appId}:{$this->appKey}:{$timestamp}:{$body}", $this->appSecret);

        $url = "{$this->host}/apps/{$this->appId}/events";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                "X-App-Key: {$this->appKey}",
                "X-App-Id: {$this->appId}",
                "X-App-Signature: {$signature}",
                "X-Timestamp: {$timestamp}",
            ],
            CURLOPT_POSTFIELDS => $body,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200;
    }
}
