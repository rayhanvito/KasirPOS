<?php

namespace App\Http\Requests\Api\Product;

use App\Classes\Common;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CheckVariantRequest extends FormRequest
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
			'purchase_price'    => 'required|gt:0',
			'sales_price'    => 'required||gt:0|gte:purchase_price',
            'opening_stock'    => 'required'
		];

        if($this->xid != '') {
            $id = Common::getIdFromHash($this->xid);

            $rules['item_code'] = [
                'required',
                Rule::unique('products', 'item_code')->where(function ($query) use ($company, $id) {
                    return $query->where('company_id', $company->id)
                        ->where('id', '!=', $id);
                })
            ];
        } else {
            $rules['item_code'] = [
                'required',
                Rule::unique('products', 'item_code')->where(function ($query) use ($company) {
                    return $query->where('company_id', $company->id);
                })
            ];
        }



		return $rules;
	}
}
