<?php

namespace App\Traits\Api\V1;

use App\Models\Api\V1\Tag;
use Carbon\Carbon;

trait TagActions {

    // this function create many tags

    public static function storeTags( $tags ) {
        $newTags = [];
        foreach ( $tags as $tagName ) {
            $newTags[] = [
                'name' => $tagName,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }

        Tag::insert( $newTags );
    }

    // this function create many tags for a specific ad

    public function storeAdTags( $request, $ad ) {
        $existingTags = Tag::whereIn( 'name', $request->tags )->pluck( 'name' )->toArray();
        $notStoredTags = array_diff( $request->tags, $existingTags );

        if ( isset( $notStoredTags ) ) {
            $this->storeTags( $notStoredTags );
        }

        $tagsIds = Tag::whereIn( 'name', $request->input( 'tags' ) )->pluck( 'id' )->toArray();
        $ad->tags()->sync( $tagsIds );
    }
}
