<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdateAdRequest extends FormRequest {
    /**
    * Determine if the user is authorized to make this request.
    */

    public function authorize(): bool {
        return true;
    }

    /**
    * Get the validation rules that apply to the request.
    *
    * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
    */

    public function rules( Request $request ): array {
        return [
            'type'          => [ 'in:buy,sale' ],
            'category_id'   => [ 'integer', 'exists:categories,id' ],
            'title'         => [ 'string', 'max:50' ],
            'description'   => [ 'string', 'max:300' ],
            'price'         => [ 'numeric', 'min:0', 'max:10000000000000' ],
            'images'        => [ 'array', 'min:1', 'max:4' ],
            'images.*'      => [ 'image'],
            'min'           => [ 'numeric', 'min:0' ],
            'max'           => [ 'numeric', 'gt:min', 'max:10000000000000' ],
            'tags'          => [ 'array' ],
            'tags.*'        => [ 'max:255', 'distinct' ],
            'phone_number'  => [ 'string', 'regex:^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$^']
        ];
    }
}
