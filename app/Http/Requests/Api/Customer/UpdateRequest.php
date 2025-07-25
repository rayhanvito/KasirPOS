<?php

namespace App\Http\Requests\Api\Customer;

use App\Classes\Common;
use App\Models\Customer;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
        $company = company();
        $loggedUser = auth('api')->user();
        $id = Common::getIdFromHash($this->route('customer'));
        $customer = Customer::find($id);

        $rules = [
            'phone'    => [
                'required', 'numeric',
                Rule::unique('users', 'phone')->where(function ($query) use ($company) {
                    return $query->where('user_type', 'customers')
                        ->where('company_id', $company->id);
                })->ignore($id)
            ],
            // 'email'    => [
            //     'required', 'email',
            //     Rule::unique('users', 'email')->where(function ($query) {
            //         return $query->where('user_type', 'customers');
            //     })->ignore($id)
            // ],
            'name'  => 'required',
            'status'     => 'required',
        ];

        if ($loggedUser->hasRole('admin') && $customer && $customer->is_walkin_customer == 0) {
            $rules['warehouse_id'] = 'required';
        }

        return $rules;
    }
}
