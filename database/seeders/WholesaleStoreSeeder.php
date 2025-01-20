<?php

namespace Database\Seeders;

use App\Enums\UserTypeEnum;
use App\Models\Product;
use App\Models\User;
use App\Models\WholesaleStore;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WholesaleStoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Wholesale Store',
            'email' => 'whole@whole.com',
            'password' => Hash::make('password'),
            'type' => UserTypeEnum::Wholesaler
        ]);

        $wholeStore = WholesaleStore::factory()->create([
            'name' => 'شركة الجملة',
            'city' => 'طرابلس',
            'address' => 'الكريمية',
            'phone' => '0910000000',

            'user_id' => $user->id,
        ]);

        $wholeStore->subscriptions()->create([
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'amount' => 1000,
        ]);

        $wholeStore->addMediaFromUrl('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR2HktmTXsnMq4zjvRmYkix1s2TnahupL9i7A&s')->toMediaCollection('logo');

        $product1 = Product::factory()->create([
            'name' => 'منتج 1',
            'description' => 'هذا المنتج رائع ومهم جدا',
            'price' => 250,
            'wholesale_store_id' => $wholeStore->id
        ]);

        $product2 = Product::factory()->create([
            'name' => 'منتج 2',
            'description' => 'هذا المنتج رائع ومهم جدا',
            'price' => 250,
            'expire_date' => now()->addYear(),
            'wholesale_store_id' => $wholeStore->id
        ]);

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
