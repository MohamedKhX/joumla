<?php

namespace Database\Seeders;

use App\Models\WholesaleStoreSubscription;
use Illuminate\Database\Seeder;

class UpdateSubscriptionStatusSeeder extends Seeder
{
    public function run(): void
    {
        WholesaleStoreSubscription::query()
            ->where('end_date', '<', now())
            ->update(['status' => 'expired']);

        WholesaleStoreSubscription::query()
            ->where('end_date', '>=', now())
            ->update(['status' => 'active']);
    }
} 