<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model {
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // relation 1 to m with ads table

    public function ads() {
        return $this->belongsToMany(Ad::class, 'ad_tags')->withTimestamps();
    }
}
