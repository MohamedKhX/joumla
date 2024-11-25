<?php

namespace App\Enums;

use App\Traits\Enum;

enum StoreTypeEnum: string
{
    use Enum;

    case GROCERY = 'grocery';
    case SUPERMARKET = 'supermarket';
    case ELECTRONICS = 'electronics';
    case CLOTHING = 'clothing';
    case FURNITURE = 'furniture';
    case PHARMACY = 'pharmacy';
    case HARDWARE = 'hardware';
    case RESTAURANT = 'restaurant';
    case CAFE = 'cafe';
    case BOOKSTORE = 'bookstore';
    case JEWELRY = 'jewelry';
    case TOY_STORE = 'toy_store';
    case BAKERY = 'bakery';
    case BUTCHER = 'butcher';
    case BEAUTY_SALON = 'beauty_salon';
    case BARBERSHOP = 'barbershop';
    case SPORTS = 'sports';
    case PET_STORE = 'pet_store';
    case STATIONERY = 'stationery';
    case GIFT_SHOP = 'gift_shop';
    case CAR_DEALER = 'car_dealer';
    case MOTORCYCLE_SHOP = 'motorcycle_shop';
    case HOME_DECOR = 'home_decor';
    case FLOWER_SHOP = 'flower_shop';
    case GARDENING = 'gardening';
    case HEALTH_AND_WELLNESS = 'health_and_wellness';
    case MOBILE_STORE = 'mobile_store';
    case COMPUTER_STORE = 'computer_store';
    case MUSIC_STORE = 'music_store';
    case ART_GALLERY = 'art_gallery';
    case FISH_MARKET = 'fish_market';
    case MEAT_MARKET = 'meat_market';
    case CONVENIENCE_STORE = 'convenience_store';
    case SECOND_HAND = 'second_hand';
    case ANTIQUES = 'antiques';
    case OUTDOOR = 'outdoor';
    case HOBBY_SHOP = 'hobby_shop';
    case AUTO_PARTS = 'auto_parts';
    case OFFICE_SUPPLIES = 'office_supplies';
    case TAILOR = 'tailor';
    case PRINTING = 'printing';
    case PHOTO_STUDIO = 'photo_studio';
}
