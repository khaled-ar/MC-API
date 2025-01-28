<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CreatePackageRequest;
use App\Http\Requests\Api\V1\UpdatePackageRequest;
use App\Models\Api\V1\Admin;
use App\Models\Api\V1\Package;
use App\Models\Api\V1\Payment;
use App\Models\Api\V1\Permission;
use App\Models\Api\V1\Subscription;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller {

    public function __construct() {
        $this->middleware( 'auth:sanctum' )->except( [ 'index', 'show' ] );
    }

    /**
    * Display a listing of the resource.
    */

    public function index() {

        $packages = Package::all();

        // add discount attributes
        foreach ( $packages as $package ) {
            $cost = $package->cost;
            $remaining_days = $package->created_at->format( 'd' ) + 5 - now()->format( 'd' );

            if ( $package->discount && $cost && $remaining_days > 0 ) {
                $cost_before_discount =
                $cost + str_pad( '1', strlen( ( string )$cost ), 0 ) * rand( 1, 9 );
                $package->cost_before_discount = $cost_before_discount;

                $package->savings_ratio = '%' . number_format(
                    ( rand( 1, 14 ) * 10 ) / rand( 1, 14 )
                );

                $package->discount_ratio = '%' . number_format(
                    ( $cost_before_discount - $cost ) * 100 / $cost_before_discount
                );

                $package->discount_message = 'باقي ' . $remaining_days . ' أيام على التخفيض';

            } else {
                $package->cost_before_discount = $cost;
                $package->savings_ratio = '%0';
                $package->discount_ratio = '%0';
                $package->discount_message = null;
            }
        }

        return response()->json( [
            'status' => 1,
            'count' => count( $packages ),
            'packages' => $packages,
        ] );
    }

    // only trashed packages

    public function onlyTrashed() {

        $this->authorize( 'viewAny', Package::class );

        $packages = Package::onlyTrashed()->get();

        return response()->json( [
            'status' => 1,
            'count' => count( $packages ),
            'packages' => $packages,
        ] );
    }

    /**
    * Store a newly created resource in storage.
    */

    public function store( CreatePackageRequest $request ) {

        // do not remove this statement
        $this->authorize( 'create', Package::class );

        $request->merge( [
            'admin_id' => $request->user()->id
        ] );

        $package = Package::forceCreate( $request->all() );

        return response()->json( [
            'status' => $package ? 1: 0,
        ] );
    }

    /**
    * Display the specified resource.
    */

    public function show( int $id ) {

        $package = Package::where( 'id', $id )->first();
        if(! $package) {
            return response()->json( [
                'status' => 0,
                'message' => 'الباقة غير موجودة',
            ] );
        }

        $role = Permission::where( 'ability', 'payments.create' )
        ->where( 'status', 'allow' )
        ->get()
        ->random();

        $admin = Admin::where( 'role_id', $role->role_id )->get();
        if ( $admin->count() == 0 ) {
            $package->admin_number = Admin::where( 'admin_id', 1 )->first()->user->phone_number;
        } else {
            $package->admin_number = $admin->random()->user->phone_number;
        }

        return response()->json( [
            'status' => 1,
            'package' => $package,
        ] );
    }

    /**
    * Update the specified resource in storage.
    */

    public function update( UpdatePackageRequest $request, int $id ) {

        $this->authorize( 'update', Package::class );

        $request->merge( [
            'admin_id' => $request->user()->id
        ] );

        $package = Package::where( 'id', $id )->update( $request->all() );
        return response()->json( [
            'status' => $package ? 1 : 0,
        ] );

    }

    /**
    * Delete the specified resource from storage.
    */

    public function delete( int $id ) {

        // the default backage can't deleting
        if ( $id == 1 ) {
            return response()->json( [
                'status' => 0,
                'message' => 'لا يمكن حذف الباقة الأساسية .'
            ] );
        }

        // do not remove this statement
        $this->authorize( 'delete', Package::class );

        try {

            DB::beginTransaction();
            Package::where( 'id', $id )->delete();
            Payment::where('package_id', $id)->delete();
            $subscriptions = Subscription::where('package_id', $id)->get();

            foreach($subscriptions as $subscription) {
                $subscription->update([
                    'package_id' => '1'
                ]);
            }

            DB::commit();
            return response()->json( [
                'status' => 1
            ] );

        } catch ( \Throwable) {
            DB::rollBack();
            return response()->json( [
                'status' => 0
            ] );
        }

    }

    /**
    * force Delete the specified resource from storage.
    */

    public function destroy( int $id ) {

        // the default backage can't deleting
        if ( $id == 1 ) {
            return response()->json( [
                'status' => 0,
                'message' => 'لا يمكن حذف الباقة الأساسية .'
            ] );
        }

        // do not remove this statement
        $this->authorize( 'delete', Package::class );

        $package = Package::where( 'id', $id )->forceDelete();
        return response()->json( [
            'status' => $package ? 1 : 0
        ] );
    }

    /**
    * restore the specified resource from storage.
    */

    public function restore( int $id ) {

        // do not remove this statement
        $this->authorize( 'restore', Package::class );

        $package = Package::where( 'id', $id )->restore();
        return response()->json( [
            'status' => $package ? 1 : 0
        ] );
    }

}
