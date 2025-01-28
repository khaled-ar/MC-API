<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LoginRequest extends FormRequest {
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
            'phone_number' => [ 'required', 'regex:^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$^' ],
            'password' => 'required',
            'remember_me' => 'boolean'
        ];
    }

    public function authenticate(): bool {

        return Auth::attempt( [
            'phone_number' => $this->phone_number,
            'password' => $this->password,
            'status' => 'active',
        ]);

    }
}
