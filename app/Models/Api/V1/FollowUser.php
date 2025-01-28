<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowUser extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id', 'follower_id'
    ];

    // relation 1 to 1 with users table [ get follower data ]

    public function follower() {
        return $this->belongsTo( User::class, 'follower_id' );
    }

    // relation 1 to 1 with users table [ get followed user data ]

    public function user() {
        return $this->belongsTo( User::class, 'user_id' );
    }
}
