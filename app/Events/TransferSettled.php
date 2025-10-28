<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransferSettled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $txId,
        public string $fromWalletId,
        public string $toWalletId,
        public float $amount
    ) {}

}
