<?php

namespace App\Models\Api\V1;

use App\Classes\Api\V1\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ad extends Model {
    use HasFactory, SoftDeletes;

    // append attributes
    protected $appends = [ 'rating', 'rating_count', 'in_favorites', 'from', 'all_images' ];
    protected $hidden = [ 'images' ];
    protected $fillable = [
        'user_id', 'category_id', 'approved_by', 'type',
        'title', 'description', 'price',
        'status', 'pinable', 'images', 'created_at',
        'updateable', 'resultable', 'location', 'phone_number' 
    ];

    // relation 1 to 1 with categories table

    public function category() {
        return $this->belongsTo( Category::class );
    }

    // relation 1 to 1 with admins table

    public function admin() {
        return $this->belongsTo( Admin::class, 'id', 'admin_id' );
    }

    // relation 1 to 1 with users table

    public function user() {
        return $this->belongsTo( User::class );
    }

    // relation 1 to m with favorites table

    public function favorites() {
        return $this->hasMany( Favorite::class );
    }

    // relation 1 to m with comments table

    public function comments() {
        return $this->hasMany( Comment::class )->where('approved_by', 'IS NOT', null);
    }

    // relation many to many with tag table

    public function tags() {
        return $this->belongsToMany( Tag::class, 'ad_tags' )->withTimestamps();
    }

    // relation 1to m with ratings table

    public function ratings() {
        return $this->morphMany( Rating::class, 'rateable' );
    }

    // relation morph to many with reports table

    public function reports() {
        return $this->morphMany( Report::class, 'reportable' );
    }

    // relation 1 to m with offers table

    public function offers() {
        return $this->hasMany( Offer::class )->where('status', 'active');
    }

    // set final rating attribute

    public function getRatingAttribute() {
        $ratings = $this->ratings();
        $ratings_count = $ratings->count();
        return $ratings_count ? round( $ratings->sum( 'value' ) / $ratings_count ) : 0;
    }

    // set rating count attribute

    public function getRatingCountAttribute() {
        return $this->ratings()->count();
    }

    // set all images attribute

    public function getAllImagesAttribute() {

        $asset = asset('/public/ads_images') . '/';
        if(is_null($this->images)) {
            return [$asset . 'default_image.jpg'];
        }

        $images = explode( '|', $this->images );

        foreach ( $images as $key => $image ) {

            if(str_starts_with($image, 'https://'))  {
                $images[$key] = $image;
                continue;
            }

            if(! file_exists('public/ads_images/' . $image)) {
                $images[ $key ] = $asset . 'default_image.jpg';
            } else {
                $images[ $key ] = $asset . '/' . $image;
            }
            
        }
        return $images;
    }

    // set in favorites attribute

    public function getInFavoritesAttribute() {

        if ( ! auth( 'sanctum' )->check() ) {
            return false;
        }

        return auth( 'sanctum' )->user()->favorites()->where( 'ad_id', $this->id )->first() ? true : false;
    }

    // set from attribute

    public function getFromAttribute() {
        return $this->created_at->diffForHumans();
    }

    // local scope active

    public function scopeActive( Builder $builder ) {
        $builder->where( 'status', 'active' );
    }

    // local scope owner

    public function scopeOwner( Builder $builder ) {
        $builder->where( 'ads.user_id', request()->user()->id );
    }

    // local scope pending

    public function scopePending( Builder $builder ) {
        $builder->where( 'status', 'pending' );
    }

    // local scope unaccept

    public function scopeUnaccept( Builder $builder ) {
        $builder->where( 'status', 'unaccept' );
    }

    //applying filters to ads

    public function ScopeFilter( $query, QueryFilter $filters ) {
        return $filters->apply( $query );
    }

    // local scope resultable

    public function scopeResultable( Builder $builder ) {
        $builder->where( 'resultable', 1 );
    }

    public function result() {
        return $this->hasOne( Result::class );
    }

    public static function booted() {
        static::addGlobalScope('check_user', function($query) {
            $query->whereRelation('user', 'deleted_at', null);
        }) ;
    }
}
