<?php

namespace App\SuperAdmin\Http\Requests\Api\PaymentSettings;

use Illuminate\Foundation\Http\FormRequest;

class RazorpayUpdateRequest extends FormRequest
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
            'razorpay_status'    => 'required',
        ];

        if ($this->razorpay_status == 'active') {
            $rules['razorpay_key'] = 'required';
            $rules['razorpay_secret'] = 'required';
            $rules['razorpay_webhook_secret'] = 'required';
        }

        return $rules;
    }
}
