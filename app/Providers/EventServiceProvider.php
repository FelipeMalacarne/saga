<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\AntiFraudApproved;
use App\Events\FundsReserved;
use App\Events\ReservationReleased;
use App\Events\TransferRejected;
use App\Events\TransferRequested;
use App\Events\TransferSettled;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TransferRequested::class => [
            //
        ],
        FundsReserved::class => [
            //
        ],
        ReservationReleased::class => [
            //
        ],
        TransferRejected::class => [
            //
        ],
        AntiFraudApproved::class => [
            //
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
