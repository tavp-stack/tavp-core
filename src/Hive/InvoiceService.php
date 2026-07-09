<?php

declare(strict_types=1);

namespace Tavp\Hive;

/**
 * Invoice generator.
 */
class InvoiceService
{
    /**
     * Generate an invoice.
     */
    public function generate(array $data): array
    {
        $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

        return [
            'invoice_number' => $invoiceNumber,
            'date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+30 days')),
            'items' => $data['items'] ?? [],
            'subtotal' => $data['subtotal'] ?? 0,
            'tax' => $data['tax'] ?? 0,
            'total' => $data['total'] ?? 0,
            'status' => 'pending',
        ];
    }

    /**
     * Mark invoice as paid.
     */
    public function markPaid(string $invoiceId): bool
    {
        return true;
    }
}
