<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model {
    use HasFactory;

    protected $fillable = [
        'reportable_type',
        'user_id',
        'reason',
        'reportable_id',
    ];

    // relation with users table

    public function reportable() {
        return $this->morphTo();
    }

    // relation with users table

    public function user() {
        return $this->belongsTo( User::class );
    }

    public static function booted() {

        static::addGlobalScope('check_user', function($query) {
            $query->whereRelation('user', 'deleted_at', null);
        }) ;

        static::addGlobalScope('check_reportable', function($query) {
            $query->whereRelation('reportable', 'deleted_at', null);
        }) ;
    }
}
