<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller {
    /**
    * Display a listing of the resource.
    */

    public function index() {
        $user = request()->user();
        $notifications = $this->customCreatedAt( $user->notifications );
        return response()->json( [
            'notifications' => $notifications,
            'count' => count( $notifications )
        ] );
    }

    public function read() {
        $user = request()->user();
        $notifications = $this->customCreatedAt( $user->readNotifications );
        return response()->json( [
            'notifications' => $notifications,
            'count' => count( $notifications )
        ] );
    }

    public function unRead() {
        $user = request()->user();
        $notifications = $this->customCreatedAt( $user->unreadNotifications );
        return response()->json( [
            'notifications' => $notifications,
            'count' => count( $notifications )
        ] );
    }

    /**
    * Return Trashed Notifications.
    */

    public function trashed() {
        $notifications = $this->customCreatedAt( request()->user()->notifications()->onlyTrashed()->get() );
        return response()->json( [
            'notifications' => $notifications,
            'count' => count( $notifications )
        ] );
    }

    /**
    * Display the specified resource.
    */

    public function show( string $notification_id ) {
        $notification = request()->user()->notifications()->withTrashed()->where( 'id', $notification_id )->first();
        $notification->markAsRead();
        $notification->from = $notification->created_at->diffForHumans();
        return response()->json( [
            'notification' => $notification
        ] );
    }

    /**
    * Delete the specified resource from storage.
    */

    public function delete( string $notification_id ) {
        $notification = request()->user()->notifications()->where( 'id', $notification_id )->delete();
        if ( $notification ) {
            return response()->json( [ 'status' => 1 ] );
        }
        return response()->json( [ 'status' => 0 ] );
    }

    /**
    * Destroy the specified resource from storage.
    */

    public function forceDelete( string $notification_id ) {
        $notification = request()->user()->notifications()
        ->onlyTrashed()
        ->where( 'id', $notification_id )
        ->forceDelete();

        return response()->json( [
            'status' => $notification ? 1 : 0
        ] );
    }

    public function restore( string $notification_id ) {
        $notification = request()->user()->notifications()
        ->onlyTrashed()
        ->where( 'id', $notification_id )
        ->restore();

        return response()->json( [
            'status' => $notification ? 1 : 0
        ] );
    }

    public function markAsRead( string $notification_id ) {
        request()->user()->notifications()
        ->where( 'id', $notification_id )
        ->first()
        ->markAsRead();

        return response()->json( [
            'status' => 1
        ] );
    }

    public function markAsUnRead( string $notification_id ) {
        request()->user()->notifications()
        ->where( 'id', $notification_id )
        ->first()
        ->markAsUnRead();

        return response()->json( [
            'status' => 1
        ] );
    }

    public function MultipleDelete( Request $request ) {
        $request->validate( [
            'ids' => [ 'required', 'array', 'min:1' ],
        ] );

        $status = 1;
        foreach ( $request->ids as $id ) {

            if ( ! ( $this->delete( $id ) )->original[ 'status' ] ) {
                $status = 0;
                break;
            }
        }

        return response()->json( [
            'status' => $status
        ] );
    }

    public function MultipleRestore( Request $request ) {
        $request->validate( [
            'ids' => [ 'required', 'array', 'min:1' ],
        ] );

        $status = 1;
        foreach ( $request->ids as $id ) {
            if ( ! ( $this->restore( $id ) )->original[ 'status' ] ) {
                $status = 0;
                break;
            }
        }

        return response()->json( [
            'status' => $status
        ] );

    }

    public function MultipleMarkAsRead( Request $request ) {
        $request->validate( [
            'ids' => [ 'required', 'array', 'min:1' ],
        ] );

        $status = 1;
        foreach ( $request->ids as $id ) {
            if ( ! ( $this->markAsRead( $id ) )->original[ 'status' ] ) {
                $status = 0;
                break;
            }
        }

        return response()->json( [
            'status' => $status
        ] );

    }

    public function MultipleMarkAsUnRead( Request $request ) {
        $request->validate( [
            'ids' => [ 'required', 'array', 'min:1' ],
        ] );

        $status = 1;
        foreach ( $request->ids as $id ) {
            if ( ! ( $this->markAsUnRead( $id ) )->original[ 'status' ] ) {
                $status = 0;
                break;
            }
        }

        return response()->json( [
            'status' => $status
        ] );

    }

    public function MultipleForceDelete( Request $request ) {
        $request->validate( [
            'ids' => [ 'required', 'array', 'min:1' ],
        ] );

        $status = 1;
        foreach ( $request->ids as $id ) {
            if ( ! ( $this->forceDelete( $id ) )->original[ 'status' ] ) {
                $status = 0;
                break;
            }
        }

        return response()->json( [
            'status' => $status
        ] );

    }

    protected function customCreatedAt( $notifications ) {
        foreach ( $notifications ?? [] as $notification ) {
            $notification->from = $notification->created_at->diffForHumans();
        }
        return $notifications;
    }
}
