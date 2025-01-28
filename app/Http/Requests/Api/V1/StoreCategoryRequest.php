<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest {
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

    public function rules(): array {
        return [
            'parent_id'     => [ 'int', 'exists:categories,id' ],
            'name'          => [ 'required', 'string', 'max:50', 'unique:categories,name' ],
            'description'   => [ 'required', 'string', 'max:500' ],
            'image'         => [ 'image' ],
            'status'        => [ 'string', 'in:active,inactive,archived' ]
        ];
    }
}
