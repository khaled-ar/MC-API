<?php

namespace App\Policies\Api\V1;

use App\Models\Api\V1\User;
use App\Models\Offer;
use Illuminate\Auth\Access\Response;

class OfferPolicy {

    // before function to skip check permissions if the user are the owner

    public function before() {
        $user = request()->user();
        if ( $user->is_admin && $user->admin->role->name === 'owner' ) {
            return true;
        }
    }

    /**
    * Determine whether the user can view any models.
    */

    public function viewAny(): bool {
        return request()->user()->hasPermission( 'offers.view' );
    }

    /**
    * Determine whether the user can view the model.
    */

    public function view(): bool {
        return request()->user()->hasPermission( 'offers.view' );
    }

    /**
    * Determine whether the user can create models.
    */

    public function create(): bool {
        return request()->user()->hasPermission( 'offers.create' );
    }

    /**
    * Determine whether the user can update the model.
    */

    public function update(): bool {
        return request()->user()->hasPermission( 'offers.update' );
    }

    /**
    * Determine whether the user can delete the model.
    */

    public function delete(): bool {
        return request()->user()->hasPermission( 'offers.delete' );
    }

    /**
    * Determine whether the user can restore the model.
    */

    public function restore(): bool {
        return request()->user()->hasPermission( 'offers.re-store' );
    }

    /**
    * Determine whether the user can permanently delete the model.
    */

    public function forceDelete(): bool {
        return request()->user()->hasPermission( 'offers.delete' );
    }

    /**
    * Determine whether the user can permanently accept the model.
    */

    public function accept(): bool {
        return request()->user()->hasPermission( 'offers.accept' );
    }
}
