<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model {
    use HasFactory;

    public $timestamps = false;

    // relation 1 to 1 with ads table

    public function ad() {
        return $this->belongsTo( Ad::class );
    }

}
