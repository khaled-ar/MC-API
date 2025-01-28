<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Api\V1\Payment;
use App\Models\Api\V1\Subscription;
use App\Notifications\Api\V1\DatabaseUserNotification;
use App\Traits\Api\V1\UpdateUserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller {

    public function index() {

        $user = request()->user();
        // admin section
        if ( $user->is_admin ) {
            $this->authorize( 'viewAny', Payment::class );
            $payments = Payment::with( [ 'user', 'admin.user', 'package:id,name,type' ] )
            ->latest( 'id' )
            ->get();

        } else {
            // user section
            $payments = $user->payments()->with( 'package' )->latest( 'id' )->get();
        }

        foreach ( $payments as $payment ) {

            if ( ! $payment->package ) {
                unset( $payment->package );
                $payment->package = collect( [
                    'id' => '---',
                    'name' => '---',
                    'type' => '---'
                ] );
            }
        }

        return response()->json( [
            'status' => 1,
            'count' => count( $payments ),
            'data' => $payments,
        ] );
    }

    public function show( int $id ) {
        $this->authorize( 'view', Payment::class );

        $payment = Payment::with( [
            'package' => fn( $query ) => $query->withTrashed(),
            'user',
            'admin.user'
        ] )
        ->where( 'id', $id )
        ->first();

        return response()->json( [
            'status' => 1,
            'data' => $payment
        ] );
    }

    public function store( Request $request ) {

        $user = $request->user();

        // check user permission
        $this->authorize( 'create', Payment::class );

        // request validation
        $data = $request->validate( [
            'user_id' => 'required|integer|exists:users,id',
            'package_id' => 'required|integer|exists:packages,id',
            'amount' => 'required|numeric',
            'currency' => 'string',
        ] );

        // store admin id
        $data[ 'admin_id' ] = $user->id;

        try {
            DB::beginTransaction();
            $payment = Payment::create( $data );
            $new_package = $payment->package;

            // get current user subscription
            $current_subscription = Subscription::where( 'user_id', $data[ 'user_id' ] )->first();
            if ( ! UpdateUserSubscription::updateOrCreate( $new_package, $current_subscription ) ) {
                return response()->json( [
                    'status' => 0,
                    'message' => 'يوجد خطأ ما, لم يتم إنشاء الدفعة'
                ] );
            }

            DB::commit();
            // send success message to the user
            $user = $payment->user;
            $message = ' لقد تم تفعيل '  . $new_package->name . ' بنجاح';
            $user->notify( new DatabaseUserNotification( $message, 'نظام الإشتراكات', 'تفعيل باقة' ) );

            return response() ->json( [
                'status' => 1,
            ] );

        } catch( \Throwable $e ) {
            DB::rollBack();
            return response()->json( [
                'status' => 0,
                'message' => $e->getMessage()
            ] );
        }
    }

    public function destroy( $id ) {
        // check user permission
        $this->authorize( 'delete', Payment::class );

        $payment = Payment::destroy( $id );

        return response()->json( [
            'status' => $payment ? 1 : 0,
        ] );
    }
}
