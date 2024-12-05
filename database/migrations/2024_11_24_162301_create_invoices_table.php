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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->date('issued_on');
            $table->decimal('total_amount');
            $table->string('number')->unique();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->foreignId('trader_id')
                ->constrained('traders')
                ->cascadeOnDelete();

            $table->foreignId('wholesale_store_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
