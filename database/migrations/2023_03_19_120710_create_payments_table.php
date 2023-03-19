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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            // Should be provided by the resource used for processing the payment.
            $table->string('transaction_id')->unique();
            // Woe won't be allowed to add new values to this field, hence string, not enum.
            $table->string('payment_method');
            $table->float('amount');
            // I wanted to use an enum, but this would mean extra work for mapping statuses.
            // Since it was not requested, for the sake of time it will be kept simple.
            $table->string('status');
            $table->json('errors')->nullable();
            $table->timestampsTz($precision = 0);;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
