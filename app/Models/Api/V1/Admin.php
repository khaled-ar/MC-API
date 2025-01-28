<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model {
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'admin_id', 'role_id',
    ];

    protected $primaryKey = 'admin_id';

    // relation 1 to 1 with users table

    public function user() {
        return $this->hasOne( User::class, 'id', 'admin_id' );
    }

    // relation 1 to 1 with roles table

    public function role() {
        return $this->hasOne( Role::class, 'id', 'role_id' );
    }

    // relation 1 to m with roles table

    public function packages() {
        return $this->hasMany( Package::class, 'admin_id', 'admin_id' );
    }

    // relation 1 to m with payments table

    public function payments() {
        return $this->hasMany( Payment::class, 'admin_id', 'admin_id' );
    }

    // relation 1 to m with categories table

    public function categories() {
        return $this->hasMany( Category::class, 'admin_id', 'admin_id' );
    }

    // relation 1 to m with ads table

    public function ads() {
        return $this->hasMany( Ad::class, 'approved_by', 'admin_id' );
    }

    // relation 1 to m with comments table

    public function comments() {
        return $this->hasMany( Comment::class, 'id', 'approved_by' );
    }

    public static function booted() {
        static::addGlobalScope('check_user', function($query) {
            $query->whereRelation('user', 'deleted_at', null);
        }) ;
    }
}
