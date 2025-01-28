<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model {
    use HasFactory, SoftDeletes;

    protected $appends = [ 'from' ];

    protected $fillable = [
        'ad_id',
        'content',
        'user_id',
        'approved_by',
    ];

    // relation 1 to 1 with user table

    public function user() {
        return $this->belongsTo( User::class );
    }

    // relation 1 to 1 with admins table

    public function admin() {
        return $this->belongsTo( Admin::class, 'approved_by', 'admin_id' );
    }

    // relation 1 to 1 with ads table

    public function ad() {
        return $this->belongsTo( Ad::class );
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
