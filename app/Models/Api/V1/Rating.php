<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id', 'rateable_id', 'rateable_type', 'value'
    ];

    // relation 1 to 1 with users table [ Evaluator ]

    public function user() {
        return $this->belongsTo( User::class );
    }

    // relation 1 to 1 with users table

    public function rateable() {
        return $this->morphTo();
    }

    // relation 1 to m with objections table

    public function objection() {
        return $this->hasMany( Objection::class );
    }

    public static function booted() {

        static::addGlobalScope('check_user', function($query) {
            $query->whereRelation('user', 'deleted_at', null);
        }) ;

        static::addGlobalScope('check_rateable', function($query) {
            $query->whereRelation('rateable', 'deleted_at', null);
        }) ;
    }
}
