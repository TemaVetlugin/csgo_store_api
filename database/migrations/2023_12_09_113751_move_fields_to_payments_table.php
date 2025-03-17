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
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id');

            $table->dropColumn('refunded_at');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onUpdate('cascade')
                ->onDelete('cascade')
            ;
        });


        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade')
            ;
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->timestamp('refunded_at')->nullable();

            $table->dropColumn('order_id');
        });

        Schema::enableForeignKeyConstraints();
    }
};
