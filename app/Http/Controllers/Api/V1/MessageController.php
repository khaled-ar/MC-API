<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Api\V1\Message;
use Illuminate\Http\Request;

class MessageController extends Controller {

    // Retrieves all messages

    public function index() {
        $this->authorize( 'viewAny', Message::class );
        $messages = Message::with( 'user' )->get();

        return response()->json( [
            'status' => 1,
            'data' => $messages,
        ] );
    }

    // Retrieves a specific message

    public function show( int $message_id ) {

        $this->authorize( 'view', Message::class );
        $message = Message::with( 'user' )->where( 'id', $message_id )->first();

        if ( !$message ) {
            return response()->json( [
                'status' => 0,
                'message' => 'الرسالة غير موجودة',
            ] );
        }

        $message->update( [ 'read_at' => now() ] );

        return response()->json( [
            'status' => 1,
            'data' => $message,
        ] );
    }

    // Stores a new message

    public function store( Request $request ) {

        // request validation
        $data = $request->validate( [
            'fname' => 'required|string',
            'lname' => 'required|string',
            'phone_number' => 'required|string',
            'content' => 'required|string',
        ] );

        $data[ 'user_id' ] = $request->user()->id;

        $message = Message::create( $data );

        return response()->json( [
            'status' => $message ? 1 : 0,
        ] );
    }

    // Permanently deletes a message ( force delete )

    public function destroy( int $id ) {

        $this->authorize( 'forceDelete', Message::class );

        $message = Message::where( 'id', $id )->forceDelete();

        return response()->json( [
            'status' => $message ? 1 : 0,
        ] );
    }
}
