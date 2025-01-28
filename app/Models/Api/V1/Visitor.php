<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model {
    use HasFactory;

    protected $fillable = [ 'ip_address' ];
}
