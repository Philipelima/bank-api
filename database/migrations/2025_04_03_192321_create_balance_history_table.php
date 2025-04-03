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
        Schema::create('balance_history', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('user_uuid')->references('uuid')->on('users')->onDelete('cascade');
            $table->decimal('balance', 15, 2)->notNull();
            $table->decimal('last_balance', 15, 2)->notNull();
            $table->decimal('change_amount', 15, 2)->notNull();
            $table->enum('operation_type', ['deposit', 'withdraw', 'transfeer'])->notNull();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_history');
    }
};
