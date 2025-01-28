<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model {
    use HasFactory, SoftDeletes;

    protected $appends = [ 'category_image', 'type' ];

    protected $hidden = [ 'image' ];

    protected $fillable = [
        'admin_id', 'parent_id', 'id',
        'name', 'description',
        'image', 'status', 'search_count'
    ];

    // relation 1 to 1 with admin table

    public function admin() {
        return $this->belongsTo( Admin::class, 'admin_id', 'admin_id' );
    }

    // relation 1 to m with categories table

    public function children() {
        return $this->hasMany( Category::class, 'parent_id' )
        ->select(['id', 'name', 'parent_id'])
        ->where( 'status', 'active' );
    }

    // relation 1 to m with ads table

    public function ads() {
        return $this->hasMany( Ad::class )->where( 'status', 'active' );
    }

    // local scope active

    public function scopeActive( Builder $builder ) {
        $builder->where( 'status', 'active' );
    }

    // get image attribute full path

    public function getCategoryImageAttribute() {

        if(str_starts_with($this->image, 'https://'))  {
            return $this->image;
        }

        return $this->image ? asset( '/public/categories_icons' ) . '/' . $this->image : null;
    }

    // get type attribute
    public function getTypeAttribute() {
        return $this->parent_id ? 'فرعية' : 'اساسية';
    }

    public function getAllDescendants()
    {
        return $this->children->flatMap(function ($child) {
            return [$child, ...$child->getAllDescendants()];
        });
    }

    public function getAllChildIds()
    {
        return $this->children->flatMap(function ($child) {
            return [$child->id, ...$child->getAllChildIds()];
        });
    }
}
