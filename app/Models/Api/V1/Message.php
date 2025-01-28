<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fname', 'lname', 'phone_number',
        'content', 'user_id', 'read_at'
    ];

    public $timestamps = false;

    // relation m to 1 with users table

    public function user() {
        return $this->belongsTo( User::class );
    }

    public static function booted() {
        static::addGlobalScope('check_user', function($query) {
            $query->whereRelation('user', 'deleted_at', null);
        }) ;
    }
}
