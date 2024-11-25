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
        Schema::create('traders', function (Blueprint $table) {
            $table->id();
            $table->string('store_name', 100);
            $table->string('phone', 20)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('store_type', 50)->nullable();
            $table->decimal('location_latitude')->nullable();
            $table->decimal('location_longitude')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traders');
    }
};
