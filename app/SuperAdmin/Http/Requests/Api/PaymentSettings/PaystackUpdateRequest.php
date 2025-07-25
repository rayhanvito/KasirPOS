<?php

namespace App\SuperAdmin\Http\Requests\Api\PaymentSettings;

use Illuminate\Foundation\Http\FormRequest;

class PaystackUpdateRequest extends FormRequest
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
            'paystack_status'    => 'required',
        ];

        if ($this->paystack_status == 'active') {
            $rules['paystack_client_id'] = 'required';
            $rules['paystack_secret'] = 'required';
            $rules['paystack_merchant_email'] = 'required';
        }

        return $rules;
    }
}
