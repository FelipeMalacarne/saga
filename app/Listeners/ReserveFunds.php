<?php

namespace App\Listeners;

use App\Events\FundsReserved;
use App\Events\TransferRequested;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReserveFunds
{
    public function handle(TransferRequested $event): void
    {
        DB::connection('wallet')->transaction(function () use ($event) {
            $wallet = Wallet::findOrFail($event->fromWalletId);
            if ($wallet->balance < $event->amount) {
                Log::warning("Insufficient funds in wallet {$wallet->id} for transfer {$event->txId}");
            }

            // Deduct the amount from the wallet balance
            $wallet->balance -= $event->amount;
            $wallet->save();

            event(new FundsReserved(
                walletId: $event->fromWalletId,
                txId: $event->txId,
                amount: $event->amount
            ));

            Log::info("Reserved {$event->amount} from wallet {$wallet->id} for transfer {$event->txId}");
        });
    }
}
