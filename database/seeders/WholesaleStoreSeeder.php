<?php

namespace Database\Seeders;

use App\Enums\UserTypeEnum;
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
    }
}
