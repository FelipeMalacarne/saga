<?php

namespace App\Enums;

enum TransferStatus: string
{
    case REQUESTED = 'requested';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case SETTLED = 'settled';
}
