<?php

namespace App\Policies\Api\V1;

use App\Models\Api\V1\User;
use App\Models\Report;
use Illuminate\Auth\Access\Response;

class ReportPolicy {

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
        return request()->user()->hasPermission( 'reports.view' );
    }

    /**
    * Determine whether the user can view the model.
    */

    public function view(): bool {
        return request()->user()->hasPermission( 'reports.view' );
    }

    /**
    * Determine whether the user can create models.
    */

    public function create(): bool {
        return request()->user()->hasPermission( 'reports.create' );
    }

    /**
    * Determine whether the user can update the model.
    */

    public function update(): bool {
        return request()->user()->hasPermission( 'reports.update' );
    }

    /**
    * Determine whether the user can delete the model.
    */

    public function delete(): bool {
        return request()->user()->hasPermission( 'reports.delete' );
    }

    /**
    * Determine whether the user can restore the model.
    */

    public function restore(): bool {
        return request()->user()->hasPermission( 'reports.re-store' );
    }

    /**
    * Determine whether the user can permanently delete the model.
    */

    public function forceDelete(): bool {
        return request()->user()->hasPermission( 'reports.forceDelete' );
    }
}
