<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    /** @use HasFactory<\Database\Factories\TransferFactory> */
    use HasFactory, HasUuids;

    protected $connection = 'transfer';

    protected $fillable = [
        'from_wallet_id',
        'to_wallet_id',
        'amount',
        'status',
    ];
}
