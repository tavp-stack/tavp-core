<?php

declare(strict_types=1);

namespace Tavp\Core\Search;

/**
 * Elasticsearch search driver.
 */
class ElasticsearchDriver implements SearchDriver
{
    private string $host;
    private string $auth;

    public function __construct(array $config)
    {
        $this->host = $config['host'] ?? 'http://localhost:9200';
        $this->auth = $config['api_key'] ?? '';
    }

    public function search(string $index, string $query, array $options = []): array
    {
        $url = "{$this->host}/{$index}/_search";

        $body = [
            'query' => [
                'multi_match' => [
                    'query' => $query,
                    'fields' => $options['fields'] ?? ['*'],
                ],
            ],
        ];

        if (isset($options['limit'])) {
            $body['size'] = $options['limit'];
        }

        $response = $this->request('POST', $url, $body);
        return $response['hits']['hits'] ?? [];
    }

    public function index(string $index, string $id, array $data): bool
    {
        $url = "{$this->host}/{$index}/_doc/{$id}";
        $response = $this->request('PUT', $url, $data);
        return isset($response['_id']);
    }

    public function delete(string $index, string $id): bool
    {
        $url = "{$this->host}/{$index}/_doc/{$id}";
        $response = $this->request('DELETE', $url);
        return $response['result'] === 'deleted';
    }

    public function createIndex(string $index, array $settings = []): bool
    {
        $url = "{$this->host}/{$index}";
        $response = $this->request('PUT', $url, $settings);
        return $response['acknowledged'] ?? false;
    }

    private function request(string $method, string $url, array $data = []): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
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
