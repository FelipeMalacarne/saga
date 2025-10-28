<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('transfer')->create('transfers', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->uuid('from_wallet_id');
            $t->uuid('to_wallet_id');
            $t->decimal('amount', 10, 2);
            $t->enum('status', ['requested', 'approved', 'rejected', 'settled']); // REQUESTED, APPROVED, REJECTED, SETTLED
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('transfer')->dropIfExists('transfers');
    }
};
