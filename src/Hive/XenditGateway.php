<?php

declare(strict_types=1);

namespace Tavp\Hive;

/**
 * Xendit payment gateway (Southeast Asia).
 */
class XenditGateway implements PaymentGateway
{
    private string $secretKey;

    public function __construct(array $config)
    {
        $this->secretKey = $config['secret_key'] ?? '';
    }

    public function createCheckout(array $data): array
    {
        $params = [
            'external_id' => $data['order_id'] ?? uniqid(),
            'amount' => $data['amount'] ?? 0,
            'currency' => $data['currency'] ?? 'IDR',
            'description' => $data['product_name'] ?? 'Subscription',
        ];

        // Call Xendit API
        return ['invoice_url' => 'https://invoice.xendit.com/...'];
    }

    public function verifyPayment(string $id): array
    {
        return ['status' => 'verified'];
    }

    public function handleWebhook(array $payload, string $signature): array
    {
        return ['type' => $payload['status'] ?? 'unknown'];
    }
}
