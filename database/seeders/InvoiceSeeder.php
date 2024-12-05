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
        $product1 = Product::factory()->create();

        //create 10 orders
        $orders = Order::factory()->create();

        foreach ($orders as $order) {
            OrderItem::factory()->create([
                'order_id'   => $order->id,
                'product_id' => $product1->id,
                'quantity'   => 1,
                'unit_price' => $product1->price,
            ]);
        }

        foreach ($orders as $order) {
            Invoice::factory()->create([

            ]);
        }
    }
}
