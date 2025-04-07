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
        Schema::create('transfers', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('payer_uuid')->references('uuid')->on('users')->onDelete('cascade');
            $table->foreignUuid('payee_uuid')->references('uuid')->on('users')->onDelete('cascade');
            $table->decimal('amount', 15, 2)->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'canceled'])->default('pending');
            $table->string('authorization_code', 100)->nullable();
            $table->timestamp('authorized_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('failed_reason', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
