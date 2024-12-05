<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //create 10 orders
        $orders = Order::factory(5)->create([
            'wholesale_store_id' => 1,
            'trader_id' => 1,
        ]);

        foreach ($orders as $order) {
            $product = Product::find(rand(1, 10));

            OrderItem::factory()->create([
                'order_id'   => $order->id,
                'product_id' => $product->id,
                'quantity'   => 1,
                'unit_price' => $product->price,
            ]);
        }

        foreach ($orders as $order) {
            Invoice::factory()->create([
                'issued_on' => now(),
                'total_amount' => $order->totalAmount,
                'number' => fake()->unique()->numberBetween(1, 100),
                'order_id' => $order->id,
                'trader_id' => 1,
                'wholesale_store_id' => 1,
            ]);
        }
    }
}
