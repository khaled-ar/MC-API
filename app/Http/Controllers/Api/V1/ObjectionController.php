<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreObjectionRequest;
use App\Http\Resources\Api\V1\Collections\ObjectionCollection;
use App\Http\Resources\Api\V1\ObjectionResource;
use App\Models\Api\V1\Objection;

class ObjectionController extends Controller {
    // Retrieves all objections

    public function index() {

        $this->authorize( 'viewAny', Objection::class );

        $objections = Objection::with( [ 'user:id,fullname,username,image', 'rating.user:id,fullname,username,image' ] )->get();
        return response()->json( [
            'status' => 1,
            'data' => new ObjectionCollection( $objections )
        ] );
    }

    // Retrieves a specific objection

    public function show( int $id ) {

        $this->authorize( 'view', Objection::class );

        $objection = Objection::firstWhere( 'id', $id );

        if ( !$objection ) {
            return response()->json( [
                'status' => 0,
                'message' => 'الاعتراض غير موجود'
            ] );
        }

        return response()->json( [
            'status' => 1,
            'data' => new ObjectionResource( $objection )
        ] );
    }

    // Stores a new objection

    public function store( StoreObjectionRequest $request ) {

        $user_id = request()->user()->id;
        Objection::create( array_merge( $request->safe()->all(), [ 'user_id' => $user_id ] ) );
        return response()->json( [
            'status' => 1,
        ] );
    }

    // Deletes an objection

    public function destroy( int $id ) {

        $this->authorize( 'delete', Objection::class );

        $objection = Objection::firstWhere( 'id', $id );

        if ( !$objection ) {
            return response()->json( [
                'status' => 0,
                'message' => 'الاعتراض غير موجود'
            ] );
        }

        $deleted = $objection->delete();
        if ( $deleted ) {
            return response()->json( [
                'status' => 1,
            ] );
        }
    }
}
