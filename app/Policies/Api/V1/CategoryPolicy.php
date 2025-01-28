<?php

namespace App\Policies\Api\V1;

use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy {

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
        return request()->user()->hasPermission( 'categories.view' );
    }

    /**
    * Determine whether the user can view the model.
    */

    public function view(): bool {
        return request()->user()->hasPermission( 'categories.view' );
    }

    /**
    * Determine whether the user can create models.
    */

    public function create(): bool {
        return request()->user()->hasPermission( 'categories.create' );
    }

    /**
    * Determine whether the user can update the model.
    */

    public function update(): bool {
        return request()->user()->hasPermission( 'categories.update' );
    }

    /**
    * Determine whether the user can delete the model.
    */

    public function delete(): bool {
        return request()->user()->hasPermission( 'categories.delete' );
    }

    /**
    * Determine whether the user can restore the model.
    */

    public function restore(): bool {
        return request()->user()->hasPermission( 'categories.re-store' );
    }

    /**
    * Determine whether the user can permanently delete the model.
    */

    public function forceDelete(): bool {
        return request()->user()->hasPermission( 'categories.delete' );
    }
}
