<?php

namespace Database\Seeders;

use App\Enums\UserTypeEnum;
use App\Models\Trader;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TraderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name'     => 'Trader',
            'email'    => 'trader@trader.com',
            'password' => Hash::make('password'),
            'type'     => UserTypeEnum::Trader
        ]);

        $trader = Trader::factory()->create([
            'store_name' => 'Trader',
            'phone' => '0910000000',
            'city' => 'طرابلس',
            'address' => 'طرابلس',
            'is_active' => true,

            'location_latitude' => 32.8752,
            'location_longitude' => 13.1875,

            'user_id' => $user->id,
        ]);
    }
}
