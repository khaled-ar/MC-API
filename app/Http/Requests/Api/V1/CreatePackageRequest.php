<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class CreatePackageRequest extends FormRequest {
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
            'name'                  => [ 'required', 'string', 'unique:packages,name' ],
            'description'           => [ 'required', 'string' ],
            'cost'                  => [ 'required', 'numeric', 'min:0', 'max:100000' ],
            'validity'              => [ 'required', 'integer', 'min:0' ],
            'type'                  => [ 'required', 'string', 'in:private,public' ],
            'discount'              => [ 'required', 'boolean' ],
            'sale_ads_validity'     => [ 'required', 'integer', 'min:0', 'max:100000' ],
            'sale_ads_limit'        => [ 'required', 'integer', 'min:0', 'max:100000' ],
            'sale_ads_updateable'   => [ 'required', 'boolean' ],
            'sale_ads_resultable'   => [ 'required', 'boolean' ],
            'buy_ads_validity'      => [ 'required', 'integer', 'min:0', 'max:100000' ],
            'buy_ads_limit'         => [ 'required', 'integer', 'min:0', 'max:100000' ],
            'buy_ads_updateable'    => [ 'required', 'boolean' ],
            'buy_ads_resultable'    => [ 'required', 'boolean' ],
            'offers_limit'          => [ 'required', 'integer', 'min:0', 'max:100000' ],
            'service_discounts'     => [ 'required', 'integer', 'min:0', 'max:100000' ],
            'hide_offer'            => [ 'required', 'boolean' ],
            'pinable'               => [ 'required', 'boolean' ],
            'offer_highlighting'    => [ 'required', 'boolean' ],
            'pinable_validity'      => [ 'required', 'integer', 'min:0', 'max:100000' ]
        ];
    }
}
