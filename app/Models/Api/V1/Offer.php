<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offer extends Model {
    use HasFactory;

    protected $appends = [ 'from' ];

    protected $fillable = [
        'user_id', 'ad_id',
        'type', 'status',
        'message', 'value',
        'offer_highlighting', 'content'
    ];

    // relation 1 to 1 with users table

    public function user() {
        return $this->belongsTo( User::class );
    }

    // relation 1 to 1 with ad table

    public function ad() {
        return $this->belongsTo( Ad::class );
    }

    // local scope visible

    public function scopeVisible( Builder $builder ) {
        $builder->where( 'type', 'visible' );
    }

    // get from attribute

    public function getFromAttribute() {
        return $this->created_at->diffForHumans();
    }

    public static function booted() {
        static::addGlobalScope('check_user', function($query) {
            $query->whereRelation('user', 'deleted_at', null);
        }) ;

        static::addGlobalScope('check_ad', function($query) {
            $query->whereRelation('ad', 'deleted_at', null);
        }) ;
    }
}
