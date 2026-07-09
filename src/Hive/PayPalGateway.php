<?php

declare(strict_types=1);

namespace Tavp\Hive;

/**
 * PayPal payment gateway.
 */
class PayPalGateway implements PaymentGateway
{
    private string $clientId;
    private string $clientSecret;

    public function __construct(array $config)
    {
        $this->clientId = $config['client_id'] ?? '';
        $this->clientSecret = $config['client_secret'] ?? '';
    }

    public function createCheckout(array $data): array
    {
        $params = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => $data['currency'] ?? 'USD',
                    'value' => number_format($data['amount'] / 100, 2, '.', ''),
                ],
                'description' => $data['product_name'] ?? 'Subscription',
            ]],
        ];

        // Call PayPal API
        return ['approval_url' => 'https://www.paypal.com/...'];
    }

    public function verifyPayment(string $id): array
    {
        return ['status' => 'verified'];
    }

    public function handleWebhook(array $payload, string $signature): array
    {
        return ['type' => $payload['event_type'] ?? 'unknown'];
    }
}
