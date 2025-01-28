<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Objection extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id', 'rating_id', 'reason'
    ];

    // relation 1 to 1 with users table

    public function user() {
        return $this->belongsTo( User::class );
    }

    // relation 1 to 1 with ratings table

    public function rating() {
        return $this->belongsTo( Rating::class);
    }

    public static function booted() {
        static::addGlobalScope('check_user', function($query) {
            $query->whereRelation('user', 'deleted_at', null);
        }) ;

        static::addGlobalScope('check_rating_user', function($query) {
            $query->whereRelation('rating.user', 'deleted_at', null);
        }) ;
    }
}
