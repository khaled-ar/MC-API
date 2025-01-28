<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreAdRequest extends FormRequest {
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
            'type'          => [ 'required', 'in:buy,sale' ],
            'category_id'   => [ 'required', 'integer', 'exists:categories,id' ],
            'title'         => [ 'required', 'string', 'max:50'],
            'description'   => [ 'required', 'string', 'max:300' ],
            'price'         => [ Rule::requiredIf( $request->type == 'sale' ), 'numeric', 'min:0', 'max:10000000000000' ],
            'images'        => [ Rule::requiredIf( $request->type == 'sale' ), 'array', 'min:1', 'max:4' ],
            'images.*'      => [ Rule::requiredIf( $request->type == 'sale' ), 'image' ],
            'min'           => [ Rule::requiredIf( $request->type == 'buy' ), 'numeric', 'min:0' ],
            'max'           => [ Rule::requiredIf( $request->type == 'buy' ), 'numeric', 'gt:min', 'max:10000000000000' ],
            'tags'          => [ 'array' ],
            'tags.*'        => [ 'max:255', 'distinct' ],
            'phone_number'  => [ 'string', 'regex:^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$^']
        ];
    }

}
