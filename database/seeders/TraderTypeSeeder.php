<?php

namespace Database\Seeders;

use App\Models\TraderType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TraderTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'بقالة', // grocery
            'سوبرماركت', // supermarket
            'إلكترونيات', // electronics
            'ملابس', // clothing
            'أثاث', // furniture
            'صيدلية', // pharmacy
            'مواد بناء', // hardware
            'مطعم', // restaurant
            'مقهى', // cafe
            'مكتبة', // bookstore
            'مجوهرات', // jewelry
            'متجر ألعاب', // toy_store
            'مخبز', // bakery
            'جزارة', // butcher
            'صالون تجميل', // beauty_salon
            'حلاق', // barbershop
            'رياضة', // sports
            'متجر حيوانات أليفة', // pet_store
            'قرطاسية', // stationery
            'متجر هدايا', // gift_shop
            'وكالة سيارات', // car_dealer
            'متجر دراجات نارية', // motorcycle_shop
            'ديكور منزلي', // home_decor
            'محل زهور', // flower_shop
            'بستنة', // gardening
            'صحة وعافية', // health_and_wellness
            'متجر جوالات', // mobile_store
            'متجر حواسيب', // computer_store
            'متجر موسيقى', // music_store
            'معرض فني', // art_gallery
            'سوق أسماك', // fish_market
            'سوق لحوم', // meat_market
            'متجر صغير', // convenience_store
            'متجر مستعمل', // second_hand
            'تحف', // antiques
            'خارجي', // outdoor
            'متجر هوايات', // hobby_shop
            'قطع غيار سيارات', // auto_parts
            'مستلزمات مكتبية', // office_supplies
            'خياط', // tailor
            'طباعة', // printing
            'استوديو تصوير', // photo_studio
        ];

        foreach ($types as $type) {
            TraderType::create([
                'name' => $type,
            ]);
        }
    }
}
