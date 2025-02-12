<?php

use App\Enums\OrderStateEnum;
use App\Enums\ShipmentStateEnum;
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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('number')->unique();
            $table->enum('state', ShipmentStateEnum::values())
                ->default(ShipmentStateEnum::Pending->value);

            $table->decimal('total_amount')->nullable();
            $table->decimal('delivery_price')->nullable();

            $table->foreignId('driver_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('trader_id')
                ->constrained('traders')
                ->cascadeOnDelete();

            $table->foreignId('area_id')
                ->constrained('areas')
                ->cascadeOnDelete();
            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
