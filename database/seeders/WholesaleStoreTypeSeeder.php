<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WholesaleStoreTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wholesaleStoreTypes = [
            'طعام', // food
            'مشروبات', // beverages
            'إلكترونيات', // electronics
            'ملابس', // clothing
            'أثاث', // furniture
            'منتجات صيدلانية', // pharmaceuticals
            'قطع غيار سيارات', // automotive_parts
            'مستلزمات صناعية', // industrial_supplies
            'منتجات زراعية', // agricultural_products
            'مواد بناء', // building_materials
            'مستلزمات مكتبية', // office_supplies
            'ألعاب', // toys
            'منتجات تجميل', // beauty_products
            'مستلزمات حيوانات أليفة', // pet_supplies
            'معدات رياضية', // sports_equipment
            'أجهزة منزلية', // home_appliances
            'كتب وقرطاسية', // books_and_stationery
            'مستلزمات حدائق', // garden_supplies
            'مستلزمات طبية', // medical_supplies
            'منسوجات', // textiles
            'أحذية', // footwear
            'مواد كيميائية', // chemicals
            'مجوهرات', // jewelry
        ];

        foreach ($wholesaleStoreTypes as $wholesaleStoreType) {
            \App\Models\WholesaleStoreType::create([
                'name' => $wholesaleStoreType,
            ]);
        }
    }
}
