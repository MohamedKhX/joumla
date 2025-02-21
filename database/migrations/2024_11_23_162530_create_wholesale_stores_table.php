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
            $table->string('phone');
            $table->decimal('location_latitude', 10, 8)->nullable();
            $table->decimal('location_longitude', 10, 8)->nullable();

            $table->softDeletes();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('wholesale_store_type_id');
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
