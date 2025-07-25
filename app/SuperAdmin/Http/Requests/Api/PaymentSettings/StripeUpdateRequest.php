<?php

namespace App\SuperAdmin\Http\Requests\Api\PaymentSettings;

use Illuminate\Foundation\Http\FormRequest;

class StripeUpdateRequest extends FormRequest
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
        $rules = [
            'stripe_status'    => 'required',
        ];

        if ($this->stripe_status == 'active') {
            $rules['stripe_api_key'] = 'required';
            $rules['stripe_api_secret'] = 'required';
            $rules['stripe_webhook_key'] = 'required';
        }

        return $rules;
    }
}
