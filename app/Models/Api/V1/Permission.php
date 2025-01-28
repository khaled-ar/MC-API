<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model {
    use HasFactory;

    protected $fillable = [ 'role_id', 'ability', 'ar_ability', 'status' ];

    // relation 1 to 1 with roles table

    public function role() {
        return $this->belongsTo( Role::class );
    }
}
