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
            'balance' => 5000,
        ]);

        $walletB = Wallet::factory()->Create([
            'balance' => 3000,
        ]);

        // create and request transfer of 450 from walletA to walletB

    }
}
