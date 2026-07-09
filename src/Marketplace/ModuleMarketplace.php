<?php

declare(strict_types=1);

namespace Tavp\Marketplace;

/**
 * Module marketplace — search, install, publish, versioning.
 */
class ModuleMarketplace
{
    private string $apiUrl;

    public function __construct(array $config)
    {
        $this->apiUrl = $config['api_url'] ?? 'https://marketplace.tavp.dev/api';
    }

    /**
     * Search for modules.
     */
    public function search(string $query, array $filters = []): array
    {
        $params = array_merge(['q' => $query], $filters);
        return $this->request('GET', '/modules', $params);
    }

    /**
     * Get module details.
     */
    public function get(string $slug): array
    {
        return $this->request('GET', "/modules/{$slug}");
    }

    /**
     * Publish a module.
     */
    public function publish(array $data): array
    {
        return $this->request('POST', '/modules', $data);
    }

    /**
     * Update a module.
     */
    public function update(string $slug, array $data): array
    {
        return $this->request('PUT', "/modules/{$slug}", $data);
    }

    /**
     * Delete a module.
     */
    public function delete(string $slug): bool
    {
        $result = $this->request('DELETE', "/modules/{$slug}");
        return isset($result['deleted']);
    }

    /**
     * Get module versions.
     */
    public function versions(string $slug): array
    {
        return $this->request('GET', "/modules/{$slug}/versions");
    }

    /**
     * Get module reviews.
     */
    public function reviews(string $slug): array
    {
        return $this->request('GET', "/modules/{$slug}/reviews");
    }

    /**
     * Add a review.
     */
    public function addReview(string $slug, array $review): array
    {
        return $this->request('POST', "/modules/{$slug}/reviews", $review);
    }

    /**
     * Install module via Composer.
     */
    public function install(string $slug): bool
    {
        $module = $this->get($slug);
        $package = $module['composer_package'] ?? "tavp/{$slug}";

        exec("composer require {$package} 2>&1", $output, $exitCode);
        return $exitCode === 0;
    }

    private function request(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->apiUrl . $endpoint;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        ]);

        if (!empty($data) && in_array($method, ['POST', 'PUT'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        if (!empty($data) && $method === 'GET') {
            $url .= '?' . http_build_query($data);
            curl_setopt($ch, CURLOPT_URL, $url);
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?? [];
    }
}
