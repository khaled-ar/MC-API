<?php

namespace App\Rules\Api\V1;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Cache;

class codeValidate implements ValidationRule {

    public $user;

    public function __construct( $user ) {
        $this->user = $user;
    }

    /**
    * Run the validation rule.
    *
    * @param  \Closure( string ): \Illuminate\Translation\PotentiallyTranslatedString  $fail
    */

    public function validate( string $attribute, mixed $value, Closure $fail ): void {

        $code = Cache::get( request()->ip() )[ 0 ] ?? null;

        if ( ! $code ) {
            $fail( 'الرجاء طلب كود تحقق اولا' );
            return;
        }

        if ( $code != $value ) {
            $fail( 'الرمز المدخل غير صحيح' );
            return;
        }
    }
}
