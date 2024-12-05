<?php

namespace Database\Seeders;

use App\Enums\UserTypeEnum;
use App\Models\Product;
use App\Models\User;
use App\Models\WholesaleStore;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WholesaleStoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory(10)->create([
            'type' => UserTypeEnum::Wholesaler
        ]);

        foreach ($users as $user) {
            WholesaleStore::factory()->create([
                'user_id' => $user->id,
            ]);
        }

        $wholeStores = WholesaleStore::all();
        foreach ($wholeStores as $wholeStore) {
            Product::factory()->create([
                'wholesale_store_id' => $wholeStore->id,
            ]);
        }

        $products = Product::all();
        foreach ($products as $product) {
            $product->addMediaFromUrl(fake()->randomElement([
                'https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=2599&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'https://images.unsplash.com/photo-1491553895911-0055eca6402d?q=80&w=2680&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?q=80&w=2670&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
            ]))->toMediaCollection('thumbnail');
        }
    }
}
