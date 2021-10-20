<?php

namespace Fafnear\IPay\Payment;

class Transaction
{
    private $info = [];

    public function __construct(float $amount, string $description, array $info = [], int $smchId = null, string $currency = null)
    {
        $this->info = [
            'amount' => round($amount, 2) * 100,
            'desc' => $description,
            'info' => json_encode($info)
        ];

        if ($smchId) {
            $this->info['smch_id'] = $smchId;
        }

        if ($currency !== null) {
            $this->info['currency'] = $currency;
        }
    }

    public function info(): array
    {
        return $this->info;
    }
}