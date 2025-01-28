<?php

namespace App\Http\Resources\Api\V1\Collections;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ObjectionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request)
    {
        return
            $this->collection->transform(function ($objection) {
                return [
                    'id' => $objection->id,
                    'reason' => $objection->reason,
                    'created_at' => $objection->created_at->format('Y-m-d'),
                    'objection_sender_id' => $objection->user->id,
                    'objection_sender_username' => $objection->user->username,
                    'objection_sender_fullname' => $objection->user->fullname,
                    'objection_sender_image' => $objection->user->full_path_image,
                    'rating_applyer_id' => $objection->rating->user->id,
                    'rating_applyer_username' => $objection->rating->user->username,
                    'rating_applyer_fullname' => $objection->rating->user->fullname,
                    'rating_applyer_image' => $objection->rating->user->full_path_image,
                    'rating_value' => $objection->rating->value,
                ];
            });
    }
}
