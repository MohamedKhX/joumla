<?php

use App\Enums\OrderStateEnum;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('number')->unique();
            $table->enum('state', OrderStateEnum::values())
                ->default(OrderStateEnum::Pending->value);

            $table->boolean('is_deferred')->default(false);

            $table->decimal('total_amount')->nullable();


            $table->foreignId('trader_id')
                ->constrained('traders')
                ->cascadeOnDelete();

            $table->foreignId('wholesale_store_id')
                ->constrained('wholesale_stores')
                ->cascadeOnDelete();

            $table->foreignId('shipment_id')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
