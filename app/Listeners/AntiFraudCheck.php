<?php

namespace App\Listeners;

use App\Enums\TransferStatus;
use App\Events\AntiFraudApproved;
use App\Events\AntiFraudRejected;
use App\Events\FundsReserved;
use App\Models\Transfer;
use Illuminate\Support\Facades\Log;

class AntiFraudCheck
{
    public function handle(FundsReserved $event): void
    {
        Log::info("Performing anti-fraud check for txId={$event->txId}");

        $tx = Transfer::find($event->txId);

        if (random_int(0, 1) === 0) {
            Log::warning("Transaction rejected by anti-fraud: txId={$event->txId}");
            $tx->status = TransferStatus::REJECTED;
            event(new AntiFraudRejected($event->txId, 'Suspeita de fraude'));
        } else {
            Log::info("Transaction approved by anti-fraud: txId={$event->txId}");
            $tx->status = TransferStatus::APPROVED;
            event(new AntiFraudApproved($event->txId));
        }

        $tx->save();
        Log::info("Anti-fraud check completed for txId={$event->txId}");
    }
}
