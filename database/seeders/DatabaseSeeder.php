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
        $this->call(TraderTypeSeeder::class);
        $this->call(WholesaleStoreTypeSeeder::class);

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'type' => UserTypeEnum::Admin,
        ]);

        $driver = User::factory()->create([
            'name' => 'The Driver',
            'email' => 'driver@driver.com',
            'password' => Hash::make('password'),
            'type' => UserTypeEnum::Driver
        ]);


        $this->call(WholesaleStoreSeeder::class);
        $this->call(TraderSeeder::class);
        $this->call(InvoiceSeeder::class);
    }
}
