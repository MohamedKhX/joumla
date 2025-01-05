<?php

namespace Database\Seeders;

use App\Enums\UserTypeEnum;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\WholesaleStore;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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

        $this->call(TraderSeeder::class);
        $this->call(WholesaleStoreSeeder::class);
        $this->call(InvoiceSeeder::class);

        $wholesale = WholesaleStore::find(1);
        $wholesale->update([
            'user_id' => $user->id,
        ]);
    }
}
