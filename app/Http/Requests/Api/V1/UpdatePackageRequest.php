<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePackageRequest extends FormRequest {
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
            'name'                  => [ 'string', "unique:packages,name,{$this->route('package')},id" ],
            'description'           => [ 'string', ],
            'cost'                  => [ 'numeric', 'min:0', 'max:100000' ],
            'validity'              => [ 'integer', 'min:0', 'max:100000' ],
            'type'                  => [ 'string', 'in:private,public' ],
            'discount'              => [ 'boolean' ],
            'sale_ads_validity'     => [ 'integer', 'min:0', 'max:100000' ],
            'sale_ads_limit'        => [ 'integer', 'min:0', 'max:100000' ],
            'sale_ads_updateable'   => [ 'boolean' ],
            'sale_ads_resultable'   => [ 'boolean' ],
            'buy_ads_validity'      => [ 'integer', 'min:0', 'max:100000' ],
            'buy_ads_limit'         => [ 'integer', 'min:0', 'max:100000' ],
            'buy_ads_updateable'    => [ 'boolean' ],
            'buy_ads_resultable'    => [ 'boolean' ],
            'offers_limit'          => [ 'integer', 'min:0', 'max:100000' ],
            'service_discounts'     => [ 'integer', 'min:0', 'max:100000' ],
            'hide_offer'            => [ 'boolean' ],
            'pinable'               => [ 'boolean' ],
            'offer_highlighting'    => [ 'boolean' ],
            'pinable_validity'      => [ 'integer', 'min:0', 'max:100000' ]
        ];
    }
}
