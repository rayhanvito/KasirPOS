<?php

namespace App\Http\Requests\Api\Warehouse;

use Illuminate\Validation\Rule;
use Vinkla\Hashids\Facades\Hashids;
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
        $convertedId = Hashids::decode($this->route('warehouse'));
        $id = $convertedId[0];

        $rules = [
            'name'    => 'required',
            'slug'    => [
                'required',
                Rule::unique('warehouses', 'slug')->where(function ($query) use ($company, $id) {
                    return $query->where('company_id', $company->id)
                        ->where('id', '!=', $id);
                })
            ],
            'email'    => 'required|email',
            'phone'    => 'required|numeric',
            'default_pos_order_status'    => 'required',
            'customers_visibility'    => 'required',
            'suppliers_visibility'    => 'required',
            'products_visibility'    => 'required',
        ];

        return $rules;
    }
}
