<?php

namespace App\Models\Api\V1;

use App\Classes\Api\V1\QueryFilter;
use App\Interfaces\Api\V1\MustVerifyMobile as IMustVerifyMobile;
use App\Traits\Api\V1\MustVerifyMobile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements IMustVerifyMobile {
    use HasFactory, Notifiable, SoftDeletes, MustVerifyMobile, HasApiTokens;

    protected $appends = [ 'full_path_image', 'rating', 'rating_count' ];

    /**
    * The attributes that should be hidden for serialization.
    *
    * @var array<int, string>
    */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'provider_token',
        'image'
    ];

    /**
    * The attributes that should be cast.
    *
    * @var array<string, string>
    */
    protected $casts = [
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    protected $fillable = [
        'username', 'fullname', 'password',
        'phone_number', 'whatsapp', 'country',
        'image', 'is_admin'
    ];

    public function tokenCan(string $ability) {

        return in_array($ability, $this->tokens()->first()->abilities);

    }

    // relation 1 to 1 with admins table

    public function admin() {
        return $this->belongsTo( Admin::class, 'id', 'admin_id' );
    }

    // relation 1 to m with payments table

    public function payments() {
        return $this->hasMany( Payment::class );
    }

    // relation 1 to m with favorites table

    public function favorites() {
        return $this->hasMany( Favorite::class )->with( [
            'ad.user' => fn( $query ) => $query->select( 'id', 'username' )
        ] );
    }

    // relation 1 to m with ads table

    public function ads() {
        return $this->hasMany( Ad::class );
    }

    // relation 1 to 1 with subscriptions table

    public function subscription() {
        return $this->hasOne( Subscription::class );
    }

    // relation morph to many with reports table

    public function reports() {
        return $this->morphMany( Report::class, 'reportable' );
    }

    // relation morph to many with ratings table

    public function ratings() {
        return $this->morphMany( Rating::class, 'rateable' );
    }

    // relation 1 to m with follow_users table

    public function followers() {
        return $this->hasMany( FollowUser::class, 'user_id' )->with( 'follower:id,username' );
    }

    // relation 1 to m with offers table

    public function offers() {
        return $this->hasMany( Offer::class );
    }

    // relation 1 to m with comments table

    public function comments() {
        return $this->hasMany( Comment::class );
    }

    // local scope filter

    public function ScopeFilter( $query, QueryFilter $filters ) {
        return $filters->apply( $query );
    }

    // check the user if has permission

    public function hasPermission( string $permission ) {

        if ( $this->is_admin ) {
            $permission = $this->admin->role->permissions()
            ->where( 'ability', $permission )->first();
            return $permission->status == 'allow';
        }
        return false;
    }

    // get image attribute full path

    public function getFullPathImageAttribute() {

        if ( str_starts_with( $this->image, 'https://' ) ) {
            return $this->image;
        }

        return $this->image ? asset( '/public/profiles_pictures' ) . '/' . $this->image : null;
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

    public function routeNotificationForTwilio() {
        return $this->phone_number;
    }
}
