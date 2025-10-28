<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\AntiFraudApproved;
use App\Events\AntiFraudRejected;
use App\Events\FundsReserved;
use App\Events\ReservationReleased;
use App\Events\TransferRequested;
use App\Events\TransferSettled;
use App\Listeners\AntiFraudCheck;
use App\Listeners\ReleaseFunds;
use App\Listeners\ReserveFunds;
use App\Listeners\SettleTransfer;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TransferRequested::class => [
            // ReserveFunds::class,
        ],
        FundsReserved::class => [
            // AntiFraudCheck::class,
        ],
        ReservationReleased::class => [
            //
        ],
        AntiFraudRejected::class => [
            // ReleaseFunds::class,
        ],
        AntiFraudApproved::class => [
            // SettleTransfer::class,
        ],
        TransferSettled::class => [
            //
        ],

    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
