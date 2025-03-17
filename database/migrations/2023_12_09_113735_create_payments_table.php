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
            $table->string('external_id');
            $table->string('payment_service')->nullable();
            $table->string('status');
            $table->timestamp('status_changed_at');
            $table->decimal('amount', 8, 2)->nullable();
            $table->string('currency')->nullable();
            $table->decimal('fee', 8, 2)->nullable();
            $table->string('resolution');
            $table->string('token')->nullable();
            $table->timestamps();
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
