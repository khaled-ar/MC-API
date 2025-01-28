<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Api\V1\Ad;
use App\Models\Api\V1\Admin;
use App\Models\Api\V1\Category;
use App\Models\Api\V1\Comment;
use App\Models\Api\V1\Message;
use App\Models\Api\V1\Objection;
use App\Models\Api\V1\Offer;
use App\Models\Api\V1\Package;
use App\Models\Api\V1\Payment;
use App\Models\Api\V1\Permission;
use App\Models\Api\V1\Rating;
use App\Models\Api\V1\Report;
use App\Models\Api\V1\Role;
use App\Models\Api\V1\User;
use App\Policies\Api\V1\AdminPolicy;
use App\Policies\Api\V1\AdPolicy;
use App\Policies\Api\V1\CategoryPolicy;
use App\Policies\Api\V1\CommentPolicy;
use App\Policies\Api\V1\MessagePolicy;
use App\Policies\Api\V1\ObjectionPolicy;
use App\Policies\Api\V1\OfferPolicy;
use App\Policies\Api\V1\PackagePolicy;
use App\Policies\Api\V1\PaymentPolicy;
use App\Policies\Api\V1\PermissionPolicy;
use App\Policies\Api\V1\RatingPolicy;
use App\Policies\Api\V1\ReportPolicy;
use App\Policies\Api\V1\RolePolicy;
use App\Policies\Api\V1\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider {
    /**
    * The model to policy mappings for the application.
    *
    * @var array<class-string, class-string>
    */
    protected $policies = [
        Admin::class        => AdminPolicy::class,
        Ad::class           => AdPolicy::class,
        Category::class     => CategoryPolicy::class,
        Comment::class      => CommentPolicy::class,
        Message::class      => MessagePolicy::class,
        Package::class      => PackagePolicy::class,
        Payment::class      => PaymentPolicy::class,
        Permission::class   => PermissionPolicy::class,
        Role::class         => RolePolicy::class,
        User::class         => UserPolicy::class,
        Rating::class       => RatingPolicy::class,
        Objection::class    => ObjectionPolicy::class,
        Offer::class        => OfferPolicy::class,
        Report::class       => ReportPolicy::class,
    ];

    /**
    * Register any authentication / authorization services.
    */

    public function boot(): void {
        $this->registerPolicies();
    }
}
