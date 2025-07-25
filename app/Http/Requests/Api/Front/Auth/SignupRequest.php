<?php

namespace App\Http\Requests\Api\Front\Auth;

use App\Models\Company;
use App\Models\Warehouse;
use App\Scopes\CompanyScope;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
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
        $storeSlug = $this->warehouse;
        $warehouse = Warehouse::select('id', 'company_id')->withoutGlobalScope(CompanyScope::class)->where('slug', $storeSlug)->first();
        $company = Company::find($warehouse->company_id);

        return [
            'name' => 'required',
            'email'    => [
                'required', 'email',
                Rule::unique('users', 'email')->where(function ($query) use ($company) {
                    return $query->where('user_type', 'customers')
                        ->where('company_id', $company->id);
                })
            ],
            'phone'    => [
                'numeric',
                Rule::unique('users', 'phone')->where(function ($query) use ($company) {
                    return $query->where('user_type', 'customers')
                        ->where('company_id', $company->id);
                })
            ],
            'password' => 'required|min:8'
        ];
    }
}
