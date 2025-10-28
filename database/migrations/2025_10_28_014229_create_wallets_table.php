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
        Schema::connection('wallet')->create('wallets', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->uuid('owner_id');
            $t->decimal('balance', 10, 2)->default(0);
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('wallet')->dropIfExists('wallets');
    }
};
