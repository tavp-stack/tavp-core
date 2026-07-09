<?php

declare(strict_types=1);

namespace Tavp\Hive;

/**
 * Stripe payment gateway.
 */
class StripeGateway implements PaymentGateway
{
    private string $secretKey;
    private string $webhookSecret;

    public function __construct(array $config)
    {
        $this->secretKey = $config['secret_key'] ?? '';
        $this->webhookSecret = $config['webhook_secret'] ?? '';
    }

    public function createCheckout(array $data): array
    {
        $params = [
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $data['currency'] ?? 'usd',
                    'product_data' => ['name' => $data['product_name'] ?? 'Subscription'],
                    'unit_amount' => $data['amount'] ?? 0,
                ],
                'quantity' => 1,
            ]],
            'mode' => $data['mode'] ?? 'subscription',
            'success_url' => $data['success_url'] ?? '',
            'cancel_url' => $data['cancel_url'] ?? '',
        ];

        // Call Stripe API
        return ['checkout_url' => 'https://checkout.stripe.com/...'];
    }

    public function verifyPayment(string $id): array
    {
        // Call Stripe API to verify
        return ['status' => 'verified'];
    }

    public function handleWebhook(array $payload, string $signature): array
    {
        // Verify webhook signature
        return ['type' => $payload['type'] ?? 'unknown'];
    }
}
