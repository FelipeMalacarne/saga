<?php

namespace App\Listeners;

use App\Events\AntiFraudRejected;
use App\Events\ReservationReleased;
use App\Models\Transfer;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReleaseFunds
{
    public function handle(AntiFraudRejected $event): void
    {
        Log::info("Releasing funds for transaction {$event->txId} due to anti-fraud rejection.");
        $tx = Transfer::find($event->txId);

        DB::connection('wallet')->transaction(function () use ($tx) {

            Wallet::whereKey($tx->from_wallet_id)
                ->increment('balance', $tx->amount);

            event(new ReservationReleased(
                walletId: $tx->from_wallet_id,
                txId: $tx->id,
                amount: $tx->amount
            ));
        });
    }
}
