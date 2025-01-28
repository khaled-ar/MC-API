<?php

namespace App\Traits\Api\V1;

use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait Images {

    // this function change name of uploaded images
    public static function giveImagesRandomNames( array $images ) {

        if ( count( $images ) == 1 ) {

            $image = $images[ 0 ][ 'name' ] ?? $images[ 0 ];

            return [ uniqid() . '.' . pathinfo( $image, PATHINFO_EXTENSION ) ];
        }

        $imagesNames = [];
        foreach ( $images as $key => $image ) {
            $imagesNames[ $key ] = uniqid() . '.' . pathinfo( $image, PATHINFO_EXTENSION );
        }
        return $imagesNames;
    }

    // this function delete images

    public static function deleteImages( array $images, string $path ) {

        foreach ( $images as $image ) {
            File::delete( $path . '/' . $image );
        }
    }

    // this function store images

    public static function storeImages( $images, array $images_names, string $path ) {

        if ( count( $images ) == 1 && $path != public_path( '/ads_images' ) ) {
            $image = new UploadedFile(, $images[ 0 ][ 'tmp_name' ], '' );
            $image = $image->move( $path, $images_names[ 0 ] );

        } else {

            for ( $i = 0; $i < count( $images );
            $i++ ) {
                $image = new UploadedFile( $images[ $i ], '' );
                $image = $image->move( $path, $images_names[ $i ] );
            }

        }

    }
}
