<?php

namespace Database\Seeders;

use App\Enums\UserTypeEnum;
use App\Models\Trader;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TraderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory(10)->create([
            'type' => UserTypeEnum::Trader
        ]);

        foreach ($users as $user) {
            Trader::factory()->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
