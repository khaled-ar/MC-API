<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Api\V1\Report;
use Illuminate\Http\Request;

class ReportController extends Controller {

    /**
    * Display a listing of the resource.
    */

    public function index() {
        $this->authorize( 'viewAny', Report::class );

        return response()->json( [
            'status' => 1,
            'data' => Report::with( [ 'user', 'reportable' ] )->get(),
        ] );
    }

    /**
    * Store a newly created resource in storage.
    */

    public function store( Request $request ) {
        // Request validation
        $request->validate( [
            'reportable_id' => [ 'required', 'integer' ],
            'reportable_type' => [ 'required', 'string', 'in:user,ad' ],
            'reason' => [ 'required', 'string' ],
        ] );
        $user = $request->user();
        $reportableType = 'App\\Models\\Api\\V1\\' . ucfirst( strtolower( $request->reportable_type ) );

        // Find the reportable model instance
        $reportable = $reportableType::find( $request->reportable_id );

        if ( !$reportable ) {
            return response()->json( [
                'status' => 0,
            ] );
        }

        // Check if a report already exists for this user and reportable item
        $existingReport = $reportable->reports()
        ->where( 'user_id', $user->id )
        ->first();

        if ( $existingReport ) {
            return response()->json( [
                'status' => 0,
            ] );
        }

        // Create the report
        $report = $reportable->reports()->create( [
            'user_id' => $user->id,
            'reason' => $request->reason,
        ] );

        if ( !$report ) {
            return response()->json( [
                'status' => 0,
            ] );
        }

        return response()->json( [
            'status' => 1,
        ] );
    }

    /**
    * Display the specified resource.
    */

    public function show( int $id ) {

        $this->authorize( 'view', Report::class );

        $report = Report::with( [ 'user', 'reportable' ] )->where( 'id', $id )->first();

        if ( ! $report ) {
            return response()->json( [
                'status' => 0,
                'message' => 'البلاغ غير موجود'
            ] );
        }

        return response()->json( [
            'status' => 1,
            'data' => $report,
        ] );
    }

    /**
    * Remove the specified resource from storage.
    */

    public function destroy( int $id ) {

        $this->authorize( 'delete', Report::class );

        $report = Report::destroy( $id );

        if ( !$report ) {
            return response()->json( [
                'status' => 0,
                'message' => 'البلاغ غير موجود'
            ] );
        }

        return response()->json( [
            'status' => 1,
        ] );
    }
}
