<?php

namespace App\Http\Requests\Api\Warehouse;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOnlineStoreStatusRequest extends FormRequest
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
            'warehouse_id'    => 'required'
        ];

        return $rules;
    }
}
