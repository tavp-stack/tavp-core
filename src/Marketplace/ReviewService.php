<?php

declare(strict_types=1);

namespace Tavp\Core\Marketplace;

/**
 * Review system — ratings and reviews for modules/themes.
 */
class ReviewService
{
    private string $apiUrl;

    public function __construct(array $config)
    {
        $this->apiUrl = $config['api_url'] ?? 'https://marketplace.tavp.dev/api';
    }

    /**
     * Get reviews for an item.
     */
    public function getReviews(string $type, string $slug, array $options = []): array
    {
        return $this->request('GET', "/{$type}/{$slug}/reviews", $options);
    }

    /**
     * Add a review.
     */
    public function addReview(string $type, string $slug, array $review): array
    {
        return $this->request('POST', "/{$type}/{$slug}/reviews", $review);
    }

    /**
     * Update a review.
     */
    public function updateReview(string $type, string $slug, string $reviewId, array $data): array
    {
        return $this->request('PUT', "/{$type}/{$slug}/reviews/{$reviewId}", $data);
    }

    /**
     * Delete a review.
     */
    public function deleteReview(string $type, string $slug, string $reviewId): bool
    {
        $result = $this->request('DELETE', "/{$type}/{$slug}/reviews/{$reviewId}");
        return isset($result['deleted']);
    }

    /**
     * Vote on a review.
     */
    public function voteReview(string $type, string $slug, string $reviewId, string $vote): bool
    {
        $result = $this->request('POST', "/{$type}/{$slug}/reviews/{$reviewId}/vote", ['vote' => $vote]);
        return isset($result['voted']);
    }

    /**
     * Get average rating.
     */
    public function getAverageRating(string $type, string $slug): float
    {
        $result = $this->request('GET', "/{$type}/{$slug}/rating");
        return $result['average'] ?? 0;
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

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?? [];
    }
}
