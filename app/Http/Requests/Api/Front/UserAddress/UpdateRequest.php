<?php

namespace App\Http\Requests\Api\Front\UserAddress;

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

		$rules = [
			'name' 	=>	'required',
			'email' 	=>	'required|required|email',
			'phone'	=>	'required|numeric',
			'address' 	=>	'required',
			'shipping_address' 	=>	'required',
			'zipcode'	=>	'required',
			'city'	=>	'required',
			'state'	=>	'required',
			'country'	=>	'required',
		];

		return $rules;
	}
}
