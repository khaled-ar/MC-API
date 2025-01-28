<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StorePermissionRequest;
use App\Http\Requests\Api\V1\UpdatePermissionRequest;
use App\Models\Api\V1\Permission;

class PermissionController extends Controller {

    // Retrieves all permissions
    public function index() {
        $this->authorize( 'viewAny', Permission::class );

        $permissions = Permission::with( 'role:id,name' )->get();
        return response()->json( [
            'status' => '1',
            'Permissions' => $permissions
        ] );
    }

    // Stores a new permission
    public function store( StorePermissionRequest $request ) {
        $this->authorize( 'create', Permission::class );

        $role = Permission::forceCreate( $request->safe()->all() );
        return response()->json( [
            'status' => 1,
            'role' => $role,
        ] );
    }

    // Updates an existing permission
    public function update( Permission $permission, UpdatePermissionRequest $request ) {
        $this->authorize( 'update', Permission::class );

        $updated = $permission->update( $request->safe()->all() );
        return response()->json( [
            'status' => $updated  ? 1 : 0,
            'permission' => $permission,
        ] );
    }

    // Deletes a permission

    public function destroy( Permission $permission ) {
        $this->authorize( 'delete', Permission::class );

        $deleted = $permission->delete();
        return response()->json( [
            'status' => $deleted ? 1 : 0
        ] );
    }
}
