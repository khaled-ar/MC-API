<?php

namespace App\Rules\Api\V1;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;

class oldPasswordValidate implements ValidationRule {

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
        if ( ! Hash::check( $value, $this->user->password ) ) {
            $fail( 'كلمة المرور الحالية غير صحيحة' );
        }
    }
}
