<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model {
    use HasFactory, SoftDeletes;

    // relation 1 to m with permissions table

    public function permissions() {
        return $this->hasMany( Permission::class );
    }
}
