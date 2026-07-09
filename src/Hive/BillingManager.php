<?php

declare(strict_types=1);

namespace Tavp\Hive;

/**
 * TAVPhive — SaaS billing manager.
 */
class BillingManager
{
    private array $gateways = [];

    public function __construct(array $config)
    {
        foreach ($config['gateways'] ?? [] as $name => $gatewayConfig) {
            $this->gateways[$name] = match ($gatewayConfig['driver'] ?? 'stripe') {
                'stripe' => new StripeGateway($gatewayConfig),
                'midtrans' => new MidtransGateway($gatewayConfig),
                'xendit' => new XenditGateway($gatewayConfig),
                'paypal' => new PayPalGateway($gatewayConfig),
                default => null,
            };
        }
    }

    /**
     * Create a checkout session.
     */
    public function createCheckout(string $gateway, array $data): array
    {
        $gw = $this->gateways[$gateway] ?? null;
        if (!$gw) {
            throw new \RuntimeException("Gateway not configured: {$gateway}");
        }

        return $gw->createCheckout($data);
    }

    /**
     * Verify a payment.
     */
    public function verifyPayment(string $gateway, string $id): array
    {
        $gw = $this->gateways[$gateway] ?? null;
        if (!$gw) {
            throw new \RuntimeException("Gateway not configured: {$gateway}");
        }

        return $gw->verifyPayment($id);
    }
}
