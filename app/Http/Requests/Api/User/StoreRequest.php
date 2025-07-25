<?php

namespace App\Http\Requests\Api\User;

use App\Classes\Common;
use App\Models\Role;
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
        $loggedUser = auth('api')->user();

        $rules = [
            'phone'    => [
                'numeric',
                Rule::unique('users', 'phone')->where(function ($query) use ($company) {
                    return $query->where(function ($query) {
                        $query->where('user_type', 'staff_members')
                            ->orWhere('user_type', 'super_admins');
                    })->where('company_id', $company->id);
                })
            ],
            'name' => 'required',
            'email'    => [
                'required', 'email',
                Rule::unique('users', 'email')->where(function ($query) use ($company) {
                    return $query->where(function ($query) {
                        $query->where('user_type', 'staff_members')
                            ->orWhere('user_type', 'super_admins');
                    })->where('company_id', $company->id);
                })
            ],
            'status' => 'required',
            'password' => 'required|min:8',
        ];

        if ($loggedUser->hasRole('admin')) {
            $rules['role_id'] = 'required';
        }

        if ($loggedUser->hasRole('admin')) {
            if ($this->has('role_id') && $this->role_id != '') {
                $roleId = Common::getIdFromHash($this->role_id);
                $role = Role::find($roleId);

                if ($role->name != 'admin') {
                    $rules['warehouse_id'] = 'required';
                }
            } else {
                $rules['warehouse_id'] = 'required';
            }
        }

        return $rules;
    }
}
