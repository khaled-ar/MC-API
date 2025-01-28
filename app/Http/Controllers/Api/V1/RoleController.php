<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdatePermissionRequest;
use App\Models\Api\V1\Role;

class RoleController extends Controller {

    // show all role with permissions

    public function index() {
        $this->authorize( 'viewAny', Role::class );

        $roles = Role::all();
        return response()->json( [
            'status' => '1',
            'roles' => $roles
        ] );
    }

    // show the role with permissions

    public function show( Role $role ) {
        $this->authorize( 'view', Role::class );

        return response()->json( [
            'status' => 1,
            'role' => $role->load( 'permissions' ),
        ] );
    }

    // Update an existing role

    public function update( Role $role, UpdatePermissionRequest $request ) {

        $this->authorize( 'update', Role::class );
        // get all application permissions from service container
        $permissions = array_keys( app( 'permissions' ) );

        try {
            foreach ( $request->permissions as $key => $value ) {
                // check if the permission and the value are valid
                if ( ! in_array( $key, $permissions ) || ! in_array( $value, [ 'allow', 'deny' ] ) ) {
                    return response()->json( [
                        'status' => 0,
                    ] );
                }
                // update the permission
                $role->permissions()->where( 'ability', $key )->update( [
                    'status' => $value,
                ] );
            }

            return response()->json( [
                'status' => 1,
            ] );

        } catch( \Throwable ) {
            return response()->json( [
                'status' => 0,
            ] );
        }
    }
}
