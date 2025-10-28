<?php

namespace App\Listeners;

use App\Enums\TransferStatus;
use App\Events\AntiFraudApproved;
use App\Events\TransferSettled;
use App\Models\Transfer;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettleTransfer
{
    public function handle(AntiFraudApproved $event): void
    {
        Log::info("Settling transfer: txId={$event->txId}");
        $tx = Transfer::find($event->txId);

        DB::connection('wallet')->transaction(function () use ($tx) {

            Wallet::whereKey($tx->to_wallet_id)
                ->increment('balance', $tx->amount);

            event(new TransferSettled(
                txId: $tx->id,
                fromWalletId: $tx->from_wallet_id,
                toWalletId: $tx->to_wallet_id,
                amount: $tx->amount,
            ));

            Log::info("Transfer settled: txId={$tx->id}");
        });

        $tx->status = TransferStatus::SETTLED;
        $tx->save();
    }
}
