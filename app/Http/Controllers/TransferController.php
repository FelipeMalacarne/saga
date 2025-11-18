<?php

namespace App\Http\Controllers;

use App\Enums\TransferStatus;
use App\Events\TransferRequested;
use App\Models\Transfer;
use App\Models\Wallet;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function create(Request $r)
    {
        $walletA = Wallet::factory()->Create([
            'name' => 'Wallet A',
            'balance' => 5000,
        ]);

        $walletB = Wallet::factory()->Create([
            'name' => 'Wallet B',
            'balance' => 3000,
        ]);

        $amountToTransfer = 450;

        $tx = Transfer::create([
            'from_wallet_id' => $walletA->id,
            'to_wallet_id' => $walletB->id,
            'amount' => $amountToTransfer,
            'status' => TransferStatus::REQUESTED,
        ]);

        event(new TransferRequested($tx->id, $walletA->id, $walletB->id, $amountToTransfer));

        return response()->json(['tx_id' => $tx->id]);
    }
}
