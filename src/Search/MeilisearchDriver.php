<?php

declare(strict_types=1);

namespace Tavp\Core\Search;

/**
 * Meilisearch search driver.
 */
class MeilisearchDriver implements SearchDriver
{
    private string $host;
    private string $apiKey;

    public function __construct(array $config)
    {
        $this->host = $config['host'] ?? 'http://localhost:7700';
        $this->apiKey = $config['api_key'] ?? '';
    }

    public function search(string $index, string $query, array $options = []): array
    {
        $url = "{$this->host}/indexes/{$index}/search";

        $data = array_merge(['q' => $query], $options);

        $response = $this->request('POST', $url, $data);
        return $response['hits'] ?? [];
    }

    public function index(string $index, string $id, array $data): bool
    {
        $url = "{$this->host}/indexes/{$index}/documents";
        $data['id'] = $id;

        $response = $this->request('POST', $url, [$data]);
        return isset($response['taskUid']);
    }

    public function delete(string $index, string $id): bool
    {
        $url = "{$this->host}/indexes/{$index}/documents/{$id}";
        $response = $this->request('DELETE', $url);
        return isset($response['taskUid']);
    }

    public function createIndex(string $index, array $settings = []): bool
    {
        $url = "{$this->host}/indexes";
        $data = ['uid' => $index] + $settings;

        $response = $this->request('POST', $url, $data);
        return isset($response['taskUid']);
    }

    private function request(string $method, string $url, array $data = []): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
        ]);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?? [];
    }
}
