<?php

namespace App\Traits\Api\V1;

trait Countries {

    protected static $ar_countries = [
        'الأردن' => 'الأردن',
        'الجزائر' => 'الجزائر',
        'البحرين' => 'البحرين',
        'جيبوتي' => 'جيبوتي',
        'مصر' => 'مصر',
        'العراق' => 'العراق',
        'الكويت' => 'الكويت',
        'لبنان' => 'لبنان',
        'ليبيا' => 'ليبيا',
        'موريتانيا' => 'موريتانيا',
        'المغرب' => 'المغرب',
        'عمان' => 'عمان',
        'فلسطين' => 'فلسطين',
        'قطر' => 'قطر',
        'المملكة العربية السعودية' => 'المملكة العربية السعودية',
        'الصومال' => 'الصومال',
        'السودان' => 'السودان',
        'سوريا' => 'سوريا',
        'تونس' => 'تونس',
        'الإمارات العربية المتحدة' => 'الإمارات العربية المتحدة',
        'اليمن' => 'اليمن',
    ];

    protected static $en_countries = [
        //
    ];

    // this function return the registred countries
    public static function getCountries( string $locale = 'ar' ) {
        if ( $locale == 'ar' ) {
            return static::$ar_countries;

        } elseif ( $locale == 'en' ) {
            return static::$en_countries;

        } else {
            return null;
        }
    }
}
