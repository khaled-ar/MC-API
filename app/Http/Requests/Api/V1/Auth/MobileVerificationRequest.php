<?php

namespace App\Http\Requests\Api\V1\Auth;

use App\Rules\Api\V1\codeValidate;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class MobileVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => ['required', 'numeric', $this->isVerified(), new codeValidate($this->user())],
        ];
    }

    public function isVerified()
    {
        return function ($attribute, $value, $fail) {
            if ($this->user()->hasVerifiedMobile()) {
                $fail('رقم هاتفكم قد تم تأكيده بالفعل');
            }
        };
    }

    /**
     * Fulfill the email verification request.
     *
     * @return void
     */
    public function fulfill()
    {
        if (! $this->user()->hasVerifiedMobile()) {
            $this->user()->markMobileAsVerified();

            event(new Verified($this->user()));
        }
    }

    /**
         * Configure the validator instance.
         *
         * @param  \Illuminate\Validation\Validator  $validator
         * @return void
         */
    public function withValidator(Validator $validator)
    {
        return $validator;
    }
}
