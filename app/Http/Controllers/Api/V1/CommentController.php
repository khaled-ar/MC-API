<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Api\V1\Comment;
use App\Notifications\Api\V1\DatabaseUserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller {

    public function __construct() {
        // $this->middleware( 'mobile.verified' )->only( [ 'store', 'update' ] );
    }

    // Retrieves all comments

    public function index() {

        $this->authorize( 'viewAny', Comment::class );

        $comments = Comment::with( 'user:id,username', 'ad' )->get();

        return response()->json( [
            'status' => 1,
            'data' => $comments,
        ] );
    }

    // Show single comment

    public function show( int $id ) {

        $this->authorize( 'view', Comment::class );
        $comment = Comment::where( 'id', $id )->with( 'user', 'ad' )->first();

        return response()->json( [
            'status' => 1,
            'comment' => $comment,
        ] );
    }

    // Stores a new comment

    public function store( Request $request ) {

        $user = $request->user();

        $data = $request->validate( [
            'ad_id' => 'required|integer|exists:ads,id',
            'content' => 'required|string|max:200',
        ] );

        $data[ 'user_id' ] = $user->id;

        $comment = Comment::where( 'ad_id', $data[ 'ad_id' ] )
        ->where( 'user_id', $user->id )
        ->where( 'content', $data[ 'content' ] )
        ->first();

        if ( $comment ) {
            return response()->json( [
                'status' => 0,
                'message' => 'التعليق موجود مسبقا'
            ] );
        }

        $comment = Comment::create( $data );

        if ( !$comment ) {
            return response()->json( [
                'status' => 0,
                'message' => 'يوجد خطأ، لم يتم إضافة التعليق',
            ] );
        }

        return response()->json( [
            'status' => 1,
        ] );
    }

    // Updates an existing comment

    public function update( Request $request, int $comment_id ) {

        $user = $request->user();
        $comment = Comment::where( 'id', $comment_id )->first();

        if ( !$comment ) {
            return response()->json( [
                'status' => 0,
                'message' => 'التعليق غير موجود'
            ] );
        }

        if ( $comment->user_id !== $user->id ) {
            return response()->json( [
                'status' => 0,
                'message' => 'غير مخول'
            ] );
        }

        $data = $request->validate( [
            'content' => 'required|string|max::255',
        ] );

        $comment->content = $data[ 'content' ];
        $comment->save();

        return response()->json( [
            'status' => 1,
            'data' => $comment,
        ] );
    }

    // Permanently deletes a comment ( force delete )

    public function destroy( int $id ) {

        $comment = Comment::destroy( $id );
        return response()->json( [
            'status' => $comment ? 1 : 0,
        ] );
    }

    // unaccept a comment

    public function unaccept( int $id ) {

        $this->authorize( 'delete', Comment::class );
        $comment = Comment::where( 'id', $id )->first();
        try {
            DB::beginTransaction();
            $ad = $comment->ad;
            $message = 'لقد تم رفض تعليقك من قبل المسؤول، ' . $ad->title;
            $comment->user->notify( new DatabaseUserNotification( $message, 'نظام التعليقات', ' رفض تعليق' ) );
            $comment->forceDelete();
            DB::commit();
            return response()->json( [
                'status' => 1,
            ] );

        } catch ( \Throwable ) {
            DB::rollBack();
            return response()->json( [
                'status' => 0,
            ] );
        }
    }

    // accept a comment

    public function accept( int $id ) {

        $this->authorize( 'approve', Comment::class );
        $comment = Comment::where( 'id', $id )->first();

        try {
            DB::beginTransaction();

            $comment->update( [
                'approved_by' => request()->user()->id,
            ] );

            $ad = $comment->ad;
            $message = 'لقد تمت الموافقة على تعليقك من قبل المسؤول، ' . $ad->title;
            $comment->user->notify( new DatabaseUserNotification( $message, 'نظام التعليقات', 'الموافقة على تعليق' ) );

            $message = ' لقد قام' . $comment->user->username . 'بالتعليق على ' . $ad->title;
            $comment->ad->user->notify( new DatabaseUserNotification( $message, 'نظام التعليقات', 'تعليق جديد' ) );

            DB::commit();
            return response()->json( [
                'status' => 1,
            ] );

        } catch ( \Throwable ) {

            DB::rollBack();
            return response()->json( [
                'status' => 0,
            ] );
        }
    }
}
