<?php

namespace App\Http\Requests\Api\V1;

use App\Traits\Api\V1\Countries;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest {
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
        $user = request()->user();
        return [
            'username' => [ 'string', 'min:3', 'regex:/\w*$/', Rule::unique( 'users', 'username' )->ignore( $this->route( 'id' ) ) ],
            'fullname' => [ 'string', 'min:3' ],
            'phone_number' => [ 'regex:^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$^', Rule::unique( 'users' )->ignore( $this->route( 'id' ) ) ],
            'whatsapp' => [ 'regex:^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$^', Rule::unique( 'users' )->ignore( $this->route( 'id' ) ) ],
            'country' => [ 'string', 'max:20', Rule::in( Countries::getCountries() ) ],
            'image' => [ 'image' ],
        ];
    }
}
