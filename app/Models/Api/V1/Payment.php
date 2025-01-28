<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id', 'admin_id', 'package_id',
        'amount', 'currency',
    ];

    // relation 1 to 1 with packages table

    public function package() {
        return $this->belongsTo( Package::class );
    }

    // relation 1 to 1 with admins table

    public function admin() {
        return $this->belongsTo( Admin::class, 'admin_id', 'admin_id' );
    }

    // relation 1 to 1 with users table

    public function user() {
        return $this->belongsTo( User::class );
    }

    public static function booted() {

        static::addGlobalScope('check_user', function($query) {
            $query->whereRelation('user', 'deleted_at', null);
        }) ;

        static::addGlobalScope('check_admin', function($query) {
            $query->whereRelation('admin.user', 'deleted_at', null);
        }) ;

        static::addGlobalScope('check_package', function($query) {
            $query->whereRelation('package', 'deleted_at', null);
        }) ;
    }
}
