<?php

namespace App\Http\Requests\Api\Brand;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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

        $rules = [
            'name'    => 'required',
            'slug'    => [
                'required',
                Rule::unique('brands', 'slug')->where(function ($query) use ($company) {
                    return $query->where('company_id', $company->id);
                })
            ],
        ];

        return $rules;
    }
}
