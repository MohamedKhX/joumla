<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            'تاجوراء' => 100,
            'طرابلس' => 150,
            'بنغازي' => 200,
            'مصراتة' => 180,
            'الزاوية' => 120,
            'سبها' => 250,
            'البيضاء' => 220,
            'درنة' => 210,
            'زليتن' => 160,
            'سرت' => 190,
            'غريان' => 140,
            'الخمس' => 130,
            'المرج' => 170,
            'اجدابيا' => 230,
            'ترهونة' => 110,
        ];

        foreach ($areas as $area => $price) {
            Area::create([
                'name'  => $area,
                'price' => $price,
            ]);
        }
    }
}
