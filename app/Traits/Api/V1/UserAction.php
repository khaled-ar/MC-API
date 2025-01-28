<?php

namespace App\Traits\Api\V1;

use App\Models\Api\V1\User;
use Illuminate\Auth\Events\Registered;

trait UserAction {
    use PhoneUpdate;
    use Images;

    // this function to store a new user

    public function storeUser( $request ) {

        $image_name = null;

        if ( isset( $_FILES[ 'image' ] ) ) {

            $image = $_FILES[ 'image' ];
            // changing image name to random string
            $image_name = Images::giveImagesRandomNames( [ $image ] )[ 0 ];

        }

        //creating user and inserting the image path to image column
        $user = User::create( array_merge(
            $request->safe()->except( 'image' ),
            [ 'image' => $image_name ]
        ) );

        if ( $user && $image_name ) {
            //storing image in public folder
            Images::storeImages( [ $image ], [ $image_name ], public_path( '/profiles_pictures' ) );
        }

        // event( new Registered( $user ) );

        return $user;
    }

    // this function to update user data

    public function updateUser( $user, $request ) {

        $image_name = null;

        if ( isset( $_FILES[ 'image' ] ) ) {

            $image = $_FILES[ 'image' ];
            // changing image name to random string
            $image_name = Images::giveImagesRandomNames( [ $image ] )[ 0 ];

            $old_image = $user->image;

            $updated = $user->update( array_merge(
                $request->safe()->except( 'image' ),
                [ 'image' => $image_name ]
            ) );

        } else {
            $updated = $user->update( $request->all() );
        }

        if ( $updated && $image_name ) {
            // delete old image
            Images::deleteImages( [ $old_image ], public_path( '/profiles_pictures' ) );

            //storing a new image
            Images::storeImages( [ $image ], [ $image_name ], public_path( '/profiles_pictures' ) );
        }

        // if ( $request->has( 'phone_number' ) ) {
        //     $this->phoneVerificationReset( $user );
        // }

        return User::where( 'id', $user->id )->first();
    }
}
