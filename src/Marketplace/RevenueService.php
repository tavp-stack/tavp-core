<?php

declare(strict_types=1);

namespace Tavp\Marketplace;

/**
 * Revenue system — sales tracking, payouts, developer dashboard.
 */
class RevenueService
{
    private string $apiUrl;

    public function __construct(array $config)
    {
        $this->apiUrl = $config['api_url'] ?? 'https://marketplace.tavp.dev/api';
    }

    /**
     * Get developer earnings summary.
     */
    public function getEarnings(int $developerId): array
    {
        return $this->request('GET', "/developers/{$developerId}/earnings");
    }

    /**
     * Get sales history.
     */
    public function getSales(int $developerId, array $filters = []): array
    {
        return $this->request('GET', "/developers/{$developerId}/sales", $filters);
    }

    /**
     * Get payout history.
     */
    public function getPayouts(int $developerId): array
    {
        return $this->request('GET', "/developers/{$developerId}/payouts");
    }

    /**
     * Request a payout.
     */
    public function requestPayout(int $developerId, array $payoutMethod): array
    {
        return $this->request('POST', "/developers/{$developerId}/payouts", $payoutMethod);
    }

    /**
     * Get transaction details.
     */
    public function getTransaction(string $transactionId): array
    {
        return $this->request('GET', "/transactions/{$transactionId}");
    }

    /**
     * Calculate developer revenue share.
     */
    public function calculateShare(float $amount, float $commissionRate = 0.20): array
    {
        $commission = $amount * $commissionRate;
        $developerShare = $amount - $commission;

        return [
            'total' => $amount,
            'commission' => $commission,
            'developer_share' => $developerShare,
            'commission_rate' => $commissionRate,
        ];
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
