<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model {
    use HasFactory;

    protected $fillable = [
        'ad_id', 'user_id', 'search_count',
        'view_count', 'share_count', 'favorited_count',
        'call_click_count', 'whatsapp_click_count',
        'messages_click_count'
    ];

    protected $hidden = [
        'actors'
    ];

    // relation 1 to 1 with ads table

    public function ad() {
        return $this->belongsTo( Ad::class );
    }

    // relation 1 to 1 with users table [ the owner of the ad ]

    public function user() {
        return $this->belongsTo( User::class );
    }

    // local scope owner

    public function scopeOwner( Builder $builder ) {
        $builder->where( 'user_id', request()->user()->id );
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
