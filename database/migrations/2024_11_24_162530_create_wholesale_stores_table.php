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
        Schema::create('wholesale_stores', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('city', 100);
            $table->string('address', 100);
            $table->enum('type', \App\Enums\WholesaleStoreEnum::values());
            $table->string('phone');
            $table->decimal('location_latitude');
            $table->decimal('location_longitude');

            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wholesale_stores');
    }
};
