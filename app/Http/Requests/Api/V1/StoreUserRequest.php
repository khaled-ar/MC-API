<?php

namespace App\Http\Requests\Api\V1;

use App\Traits\Api\V1\Countries;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest {
    /**
    * Determine if the user is authorized to make this request.
    */

    public function authorize(): bool {
        if ( request()->user() ) {
            return request()->user()->hasPermission( 'users.create' );
        }
        return true;
    }

    /**
    * Get the validation rules that apply to the request.
    *
    * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
    */

    public function rules(): array {
        return [
            'username' => [ 'required', 'string', 'min:3', 'max:20', 'unique:users,username', 'regex:/\w*$/' ],
            'fullname' => [ 'required', 'string', 'min:3', 'max:40' ],
            'phone_number' => [ 'required', 'regex:^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$^', 'unique:users,phone_number' ],
            'whatsapp' => [ 'required', 'regex:^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$^', 'unique:users,whatsapp' ],
            'country' => [ 'required', 'string', 'max:50', Rule::in( Countries::getCountries() ) ],
            'image' => [ 'image'],
            'password' => [ 'required', 'confirmed', Password::min( 8 )->letters()->numbers() ],
            'accept_terms' => [ 'accepted' ]
        ];
    }
}
