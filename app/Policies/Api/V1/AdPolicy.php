<?php

namespace App\Policies\Api\V1;

use Illuminate\Auth\Access\HandlesAuthorization;

class AdPolicy {

    use HandlesAuthorization;

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
        return request()->user()->hasPermission( 'ads.view' );
    }

    /**
    * Determine whether the user can view the model.
    */

    public function view(): bool {
        return request()->user()->hasPermission( 'ads.view' );
    }

    /**
    * Determine whether the user can create models.
    */

    public function create(): bool {
        return request()->user()->hasPermission( 'ads.create' );
    }

    /**
    * Determine whether the user can update the model.
    */

    public function update(): bool {
        return request()->user()->hasPermission( 'ads.update' );
    }

    /**
    * Determine whether the user can delete the model.
    */

    public function delete(): bool {
        return request()->user()->hasPermission( 'ads.delete' );
    }

    /**
    * Determine whether the user can restore the model.
    */

    public function restore(): bool {
        return request()->user()->hasPermission( 'ads.re-store' );
    }

    /**
    * Determine whether the user can permanently delete the model.
    */

    public function forceDelete(): bool {
        return request()->user()->hasPermission( 'ads.delete' );
    }

    /**
    * Determine whether the user can permanently accept the model.
    */

    public function accept(): bool {
        return request()->user()->hasPermission( 'ads.accept' );
    }

    /**
    * Determine whether the user can permanently unaccept the model.
    */

    public function unaccept(): bool {
        return request()->user()->hasPermission( 'ads.unaccept' );
    }
}
