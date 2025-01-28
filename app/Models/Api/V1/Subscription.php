<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id', 'package_id',
        'sale_ads_validity', 'sale_ads_limit',
        'buy_ads_validity', 'buy_ads_limit',
        'offers_limit', 'service_discounts',
    ];

    // relation 1 to 1 with packages table

    public function package() {
        return $this->belongsTo( Package::class );
    }

    public static function booted() {

        static::addGlobalScope('check_package', function($query) {
            $query->whereRelation('package', 'deleted_at', null);
        }) ;
    }
}
