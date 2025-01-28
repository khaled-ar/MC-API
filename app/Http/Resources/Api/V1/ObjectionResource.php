<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Api\V1\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ObjectionResource extends JsonResource {
    /**
    * Transform the resource into an array.
    *
    * @return array<string, mixed>
    */

    public function toArray( Request $request ): array {
        return [
            'id' => $this->id,
            'reason' => $this->reason,
            'created_at' => $this->created_at->format( 'Y-m-d' ),
            'objection_sender_id' => $this->user->id,
            'objection_sender_username' => $this->user->username,
            'objection_sender_fullname' => $this->user->fullname,
            'objection_sender_image' => $this->user->full_path_image,
            'rating_applyer_id' => $this->rating->user->id,
            'rating_applyer_username' => $this->rating->user->fullname,
            'rating_applyer_fullname' => $this->rating->user->username,
            'rating_applyer_image' => $this->rating->user->full_path_image,
            'rating_value' => $this->rating->value,
            'rateable_type' => $this->rating->rateable_type == User::class ? 'User' : 'Ad',
            'rateable' => $this->rating->rateable
        ];
    }
}
