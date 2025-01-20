<?php

namespace Database\Seeders;

use App\Enums\OrderStateEnum;
use App\Enums\UserTypeEnum;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\WholesaleStore;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Product;
use App\Models\Trader;
use App\Models\TraderType;
use App\Models\WholesaleStoreType;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),

            'type' => UserTypeEnum::Admin,
        ]);

        User::factory(5)->create([
            'type' => UserTypeEnum::Driver
        ]);

        $this->call(TraderTypeSeeder::class);
        $this->call(WholesaleStoreTypeSeeder::class);
        $this->call(WholesaleStoreSeeder::class);
        $this->call(TraderSeeder::class);
        $this->call(WholesaleStoreSeeder::class);
        $this->call(InvoiceSeeder::class);

        $wholesale = WholesaleStore::find(1);
        $wholesale->update([
            'user_id' => $user->id,
        ]);

        // Create wholesale store owners
        User::factory(10)
            ->hasWholesaleStore(1, function () {
                return [
                    'wholesale_store_type_id' => WholesaleStoreType::inRandomOrder()->first()->id,
                ];
            })
            ->create();

        // Create traders
        User::factory(30)
            ->hasTrader(1, function () {
                return [
                    'trader_type_id' => TraderType::inRandomOrder()->first()->id,
                ];
            })
            ->create();

        // Create products for each wholesale store
        WholesaleStore::all()->each(function ($store) {
            Product::factory(rand(5, 15))->create([
                'wholesale_store_id' => $store->id,
            ]);
        });

        // Create subscriptions
        WholesaleStore::all()->each(function ($store) {
            $store->subscriptions()->create([
                'start_date' => now()->subMonths(rand(1, 6)),
                'end_date' => now()->addMonths(rand(1, 12)),
                'amount' => fake()->randomElement([100, 150, 200, 250, 300]),
                'status' => 'active',
            ]);
        });

        // Create some orders
        Trader::all()->each(function ($trader) {
            $wholesaleStores = WholesaleStore::inRandomOrder()->take(rand(1, 3))->get();

            foreach ($wholesaleStores as $store) {
                $order = $trader->orders()->create([
                    'wholesale_store_id' => $store->id,
                    'date' => now()->subDays(rand(1, 30)),
                    'state' => fake()->randomElement(OrderStateEnum::values()),
                ]);

                $products = $store->products->random(rand(1, 5));
                foreach ($products as $product) {
                    $order->items()->create([
                        'product_id' => $product->id,
                        'quantity' => rand(1, 10),
                        'unit_price' => $product->price,
                    ]);
                }
            }
        });
    }
}
