<?php

namespace App\Http\Requests\Api\Product;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Vinkla\Hashids\Facades\Hashids;

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
        $convertedId = Hashids::decode($this->route('product'));
        $id = $convertedId[0];

        $rules = [
            'name'    => 'required',
            'slug'    => [
                'required',
                Rule::unique('products', 'slug')->where(function ($query) use ($company, $id) {
                    return $query->where('company_id', $company->id)
                        ->where('id', '!=', $id);
                })
            ],
            'barcode_symbology'    => 'required',
            'item_code'    => [
                'required',
                Rule::unique('products', 'item_code')->where(function ($query) use ($company, $id) {
                    return $query->where('company_id', $company->id)
                        ->where('id', '!=', $id);
                })
            ],
            'category_id'    => 'required',
            'unit_id'    => 'required'
        ];

        if($this->product_type == 'single') {
            $rules['purchase_price'] = 'required|gt:0';
            $rules['sales_price'] = 'required||gt:0|gte:purchase_price';
        }

        // If purchase or sales includes tax
        if ($this->purchase_tax_type == 'inclusive' || $this->sales_tax_type == 'inclusive') {
            $rules['tax_id'] = 'required';
        }

        return $rules;
    }
}
