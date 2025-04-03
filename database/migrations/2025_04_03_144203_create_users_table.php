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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('first_name', 100)->notNull();
            $table->string('last_name', 100)->notNull();
            $table->string('document', 18)->notNull()->unique();
            $table->enum('user_type', ['common', 'merchant']);
            $table->string('email')->notNull()->unique();
            $table->string('password')->NotNull();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
