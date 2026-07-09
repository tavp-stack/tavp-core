<?php

declare(strict_types=1);

namespace Tavp\Hive;

/**
 * Midtrans payment gateway (Indonesia).
 */
class MidtransGateway implements PaymentGateway
{
    private string $serverKey;
    private string $clientKey;

    public function __construct(array $config)
    {
        $this->serverKey = $config['server_key'] ?? '';
        $this->clientKey = $config['client_key'] ?? '';
    }

    public function createCheckout(array $data): array
    {
        $params = [
            'transaction_details' => [
                'order_id' => $data['order_id'] ?? uniqid(),
                'gross_amount' => $data['amount'] ?? 0,
            ],
            'item_details' => [[
                'id' => $data['product_id'] ?? '001',
                'name' => $data['product_name'] ?? 'Subscription',
                'price' => $data['amount'] ?? 0,
                'quantity' => 1,
            ]],
            'customer_details' => [
                'first_name' => $data['name'] ?? '',
                'email' => $data['email'] ?? '',
            ],
        ];

        // Call Midtrans API
        return ['redirect_url' => 'https://app.sandbox.midtrans.com/...'];
    }

    public function verifyPayment(string $id): array
    {
        // Call Midtrans API
        return ['status' => 'verified'];
    }

    public function handleWebhook(array $payload, string $signature): array
    {
        return ['type' => $payload['transaction_status'] ?? 'unknown'];
    }
}
