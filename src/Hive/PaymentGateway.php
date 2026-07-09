<?php

declare(strict_types=1);

namespace Tavp\Hive;

interface PaymentGateway
{
    public function createCheckout(array $data): array;
    public function verifyPayment(string $id): array;
    public function handleWebhook(array $payload, string $signature): array;
}
