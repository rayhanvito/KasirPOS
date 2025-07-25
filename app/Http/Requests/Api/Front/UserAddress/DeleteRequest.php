<?php

namespace App\Http\Requests\Api\Front\UserAddress;

use App\Models\UserAddress;
use Illuminate\Foundation\Http\FormRequest;
use Vinkla\Hashids\Facades\Hashids;

class DeleteRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */

	public function authorize()
	{
		$convertedId = Hashids::decode($this->address);
		$address = $convertedId[0];

		$user = auth('api_front')->user();
		$address = UserAddress::find($address);

		if ($user->id == $address->user_id) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [];
	}
}
