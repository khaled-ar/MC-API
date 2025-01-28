<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Api\V1\Ad;
use App\Models\Api\V1\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller {

    /**
    * Display a listing of the resource.
    */

    public function index() {
        $this->authorize( 'viewAny', Rating::class );

        return response()->json( [
            'status' => 1,
            'data' => Rating::where( 'rateable_type', Ad::class )->with( [ 'user', 'rateable' ] )->get(),
        ] );
    }

    /**
    * Store a newly created resource in storage.
    */

    public function store( Request $request ) {

        // request validation
        $request->validate( [
            'rateable_id' => [ 'required', 'integer' ],
            'rateable_type' => [ 'required', 'string', 'in:user,ad' ],
            'value' => [ 'required', 'integer', 'min:0', 'max:5' ],
        ] );

        $user = $request->user();
        // add user id and rateable type values to request data
        $request->merge( [
            'user_id' => $user->id,
            'rateable_type' => 'App\\Models\\Api\\V1\\' . ucfirst( strtolower( $request->rateable_type ) )
        ] );

        // get old rating
        $rating = Rating::where( 'user_id', $user->id )
        ->where( 'rateable_id', $request->rateable_id )
        ->where( 'rateable_type', $request->rateable_type )
        ->first();

        // check if the rating already exists
        if ( $rating ) {
            $rating = $rating->update( [ 'value' => $request->value ] );
            if ( !$rating ) {
                return response()->json( [
                    'status' => 0,
                    'message' => 'لم يتم تحديث التقييم'
                ] );
            }
            return response()->json( [ 'status' => 1 ] );
        }
        // create a new rating
        $rating = Rating::create( [
            'user_id' => $user->id,
            'rateable_id' => $request->rateable_id,
            'rateable_type' => $request->rateable_type,
            'value' => $request->value
        ] );

        if ( !$rating ) {
            return response()->json( [
                'status' => 0,
                'message' => 'لم يتم إضافة التقييم'
            ] );
        }

        return response()->json( [ 'status' => 1 ] );
    }

    /**
    * Display the specified resource.
    */

    public function show( int $id ) {

        $this->authorize( 'view', Rating::class );

        $rating = Rating::with( [ 'user', 'rateable' ] )->where( 'id', $id )->first();

        if ( !$rating ) {
            return response()->json( [
                'status' => 0,
                'message' => 'التقييم غير موجود'
            ] );
        }

        return response()->json( [
            'status' => 1,
            'data' => $rating,
        ] );
    }

    /**
    * Remove the specified resource from storage.
    */

    public function destroy( int $id ) {

        $this->authorize( 'delete', Rating::class );

        $rating = Rating::destroy( $id );

        if ( !$rating ) {
            return response()->json( [
                'status' => 0,
                'message' => 'التقييم غير موجود'
            ] );
        }

        return response()->json( [
            'status' => 1,
        ] );
    }
}
