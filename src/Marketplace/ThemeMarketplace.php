<?php

declare(strict_types=1);

namespace Tavp\Core\Marketplace;

/**
 * Theme marketplace — browse, preview, install, customize.
 */
class ThemeMarketplace
{
    private string $apiUrl;

    public function __construct(array $config)
    {
        $this->apiUrl = $config['api_url'] ?? 'https://marketplace.tavp.dev/api';
    }

    /**
     * Search for themes.
     */
    public function search(string $query, array $filters = []): array
    {
        $params = array_merge(['q' => $query, 'type' => 'theme'], $filters);
        return $this->request('GET', '/themes', $params);
    }

    /**
     * Get theme details.
     */
    public function get(string $slug): array
    {
        return $this->request('GET', "/themes/{$slug}");
    }

    /**
     * Publish a theme.
     */
    public function publish(array $data): array
    {
        return $this->request('POST', '/themes', $data);
    }

    /**
     * Install theme.
     */
    public function install(string $slug): bool
    {
        $theme = $this->get($slug);
        $downloadUrl = $theme['download_url'] ?? '';

        if (empty($downloadUrl)) {
            return false;
        }

        // Download and extract theme
        $tempFile = tempnam(sys_get_temp_dir(), 'tavp_theme');
        file_put_contents($tempFile, file_get_contents($downloadUrl));

        $zip = new \ZipArchive();
        if ($zip->open($tempFile) === true) {
            $themesPath = base_path('resources/themes');
            $zip->extractTo($themesPath);
            $zip->close();
            unlink($tempFile);
            return true;
        }

        return false;
    }

    /**
     * Get theme preview.
     */
    public function preview(string $slug): string
    {
        return $this->request('GET', "/themes/{$slug}/preview")['url'] ?? '';
    }

    /**
     * Rate a theme.
     */
    public function rate(string $slug, int $rating, string $review = ''): bool
    {
        $result = $this->request('POST', "/themes/{$slug}/rate", [
            'rating' => $rating,
            'review' => $review,
        ]);
        return isset($result['rated']);
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

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?? [];
    }
}
