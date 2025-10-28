<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReservationReleased
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $walletId,
        public string $txId,
        public float $amount
    ) {}

}
