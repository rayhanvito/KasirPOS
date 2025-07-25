<?php

namespace App\Http\Requests\Api\User;

use App\Models\User;
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
        $loggedUser = auth('api')->user();
        $convertedId = Hashids::decode($this->route('user'));
        $id = $convertedId[0];

        $rules = [
            'phone'    => [
                'numeric',
                Rule::unique('users', 'phone')->where(function ($query) use ($company) {
                    return $query->where(function ($query) {
                        $query->where('user_type', 'staff_members')
                            ->orWhere('user_type', 'super_admins');
                    })->where('company_id', $company->id);
                })->ignore($id)
            ],
            'name' => 'required',
            'email'    => [
                'required', 'email',
                Rule::unique('users', 'email')->where(function ($query) use ($company) {
                    return $query->where(function ($query) {
                        $query->where('user_type', 'staff_members')
                            ->orWhere('user_type', 'super_admins');
                    })->where('company_id', $company->id);
                })->ignore($id)
            ],
            'status' => 'required',
        ];

        if ($loggedUser->hasRole('admin')) {
            $rules['role_id'] = 'required';
        }

        // No need to add warehouse for admin user
        if ($loggedUser->hasRole('admin')) {
            $editUser = User::with(['role'])->find($id);
            if ($editUser->role && $editUser->role->name != 'admin') {
                $rules['warehouse_id'] = 'required';
            }
        }

        if ($this->password != '') {
            $rules['password'] = 'required|min:8';
        }

        return $rules;
    }
}
