<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Api\V1\Admin;
use App\Models\Api\V1\Role;
use App\Models\Api\V1\User;
use App\Notifications\Api\V1\DatabaseUserNotification;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller {

    // this function return all admins

    public function index() {
        $this->authorize( 'viewAny', Admin::class );
        return response()->json( [
            'status' => 1,
            'admins' => Admin::with( [ 'user', 'role' ] )->get(),
        ] );
    }

    // this function return single admin

    public function show( Admin $admin ) {
        $this->authorize( 'view', Admin::class );
        return response()->json( [
            'status' => 1,
            'data' => $admin->load( [
                'user',
                'role'
            ] ),
        ] );
    }

    // this function update or store admin

    public function updateOrStore( User $user, Role $role ) {
        $this->authorize( 'create', Admin::class );
        try {
            DB::beginTransaction();
            // set user as admin
            $user->update( [
                'is_admin' => '1',
            ] );

            // set the given role to user
            $user->admin()->updateOrCreate( [
                'admin_id' => $user->id,
            ], [
                'role_id' => $role->id
            ] );

            // notify the user
            $message = 'لقد قام المسؤول بتعديل صلاحياتك';
            $user->notify( new DatabaseUserNotification( $message, 'نظام المسؤولين', 'تعديل الصلاحيات' ) );
            DB::commit();

            return response()->json( [
                'status' => 1,
            ] );

        } catch( \Throwable ) {
            DB::rollBack();
            return response()->json( [
                'status' => 0,
            ] );
        }
    }

    // this function delete exists admin

    public function delete( User $user ) {
        $this->authorize( 'delete', Admin::class );
        try {

            DB::beginTransaction();
            $user->update( [
                'is_admin' => '0',
            ] );
            $user->admin()->delete();
            // notify the user
            $message = 'لقد قام المسؤول بحذف صلاحياتك';
            $user->notify( new DatabaseUserNotification( $message, 'نظام المسؤولين', 'حذف الصلاحيات' ) );
            DB::commit();
            return response()->json( [
                'status' => 1,
            ] );

        } catch( \Throwable ) {
            DB::rollBack();
            return response()->json( [
                'status' => 0,
            ] );
        }
    }
}
