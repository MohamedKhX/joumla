<?php

namespace App\Enums;

use App\Traits\Enum;

enum WholesaleStoreEnum: string
{
    use Enum;

    case FOOD = 'food';
    case BEVERAGES = 'beverages';
    case ELECTRONICS = 'electronics';
    case CLOTHING = 'clothing';
    case FURNITURE = 'furniture';
    case PHARMACEUTICALS = 'pharmaceuticals';
    case AUTOMOTIVE_PARTS = 'automotive_parts';
    case INDUSTRIAL_SUPPLIES = 'industrial_supplies';
    case AGRICULTURAL_PRODUCTS = 'agricultural_products';
    case BUILDING_MATERIALS = 'building_materials';
    case OFFICE_SUPPLIES = 'office_supplies';
    case TOYS = 'toys';
    case BEAUTY_PRODUCTS = 'beauty_products';
    case PET_SUPPLIES = 'pet_supplies';
    case SPORTS_EQUIPMENT = 'sports_equipment';
    case HOME_APPLIANCES = 'home_appliances';
    case BOOKS_AND_STATIONERY = 'books_and_stationery';
    case GARDEN_SUPPLIES = 'garden_supplies';
    case MEDICAL_SUPPLIES = 'medical_supplies';
    case TEXTILES = 'textiles';
    case FOOTWEAR = 'footwear';
    case CHEMICALS = 'chemicals';
    case JEWELRY = 'jewelry';
}
