<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TransferRequested
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $txId,
        public string $fromWalletId,
        public string $toWalletId,
        public float $amount
    ) {
        Log::info("TransferRequested event created: txId={$txId}, fromWalletId={$fromWalletId}, toWalletId={$toWalletId}, amount={$amount}");
    }
}
