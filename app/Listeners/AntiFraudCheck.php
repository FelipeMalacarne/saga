<?php

namespace App\Listeners;

use App\Events\AntiFraudApproved;
use App\Events\AntiFraudRejected;
use App\Events\FundsReserved;

class AntiFraudCheck
{
    public function handle(FundsReserved $event): void
    {
        if (random_int(0, 1) === 0) {
            event(new AntiFraudRejected($event->txId, 'Suspeita de fraude'));
        } else {
            event(new AntiFraudApproved($event->txId));
        }
    }
}
