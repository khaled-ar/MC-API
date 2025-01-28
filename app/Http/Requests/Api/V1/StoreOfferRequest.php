<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfferRequest extends FormRequest {
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
            'ad_id' => [ 'required', 'exists:ads,id' ],
            'content' => [ 'required', 'string', 'max:255' ],
            'value' => [ 'required', 'numeric' ]
        ];
    }

    public function canHide() {
        return function ( $attribute, $value, $fail ) {
            if ( request()->type == 'hidden' ) {
                if ( !$this->user()->subscription->package->hide_offer ) {
                    $fail( 'لا تملك صلاحية إخفاء العرض' );
                }
            }
        }
        ;
    }
}
