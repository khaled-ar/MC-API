<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admin_id', 'name', 'description', 'type',
        'cost', 'validity', 'discount',
        'sale_ads_validity', 'sale_ads_limit',
        'buy_ads_validity', 'buy_ads_limit',
    ];

    // relation 1 to m with payments table

    public function payments() {
        return $this->hasMany( Payment::class );
    }

    // relation 1 to m with subscriptions table

    public function subscription() {
        return $this->hasOne( Subscription::class );
    }

    // relation 1 to 1 with admins table

    public function admin() {
        return $this->belongsTo( Admin::class, 'id', 'admin_id' );
    }
}
