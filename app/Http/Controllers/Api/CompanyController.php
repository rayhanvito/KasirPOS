<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Company\UpdateRequest;
use App\Models\Company;
use App\Models\Settings;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Http\Request;

class CompanyController extends ApiBaseController
{
    protected $model = Company::class;

    protected $updateRequest = UpdateRequest::class;

    public function updating(Company $company)
    {
        if (env('APP_ENV') == 'production' && ($company->isDirty('name') ||
            $company->isDirty('short_name') || $company->isDirty('light_logo') ||
            $company->isDirty('dark_logo') || $company->isDirty('small_dark_logo') ||
            $company->isDirty('small_light_logo') || $company->isDirty('app_debug') ||
            $company->isDirty('update_app_notification') || $company->isDirty('app_debug')
        )) {
            throw new ApiException('Not Allowed In Demo Mode');
        }

        return $company;
    }

    public function updated(Company $company)
    {
        $user = user();
        if ($company->warehouse_id != null && $user->warehouse_id == null && $user->hasRole('admin')) {
            $user->warehouse_id = $company->warehouse_id;
            $user->save();

            $user = auth('api')->user();
            $user = $user->load('role', 'role.perms', 'warehouse', 'userWarehouses');

            session(['user' => $user]);
        }

        return $company;
    }

    public function updateCreateMenu(Request $request)
    {
        $company = company();
        $company->shortcut_menus = $request->position;
        $company->save();

        // Setting for create menu
        $settingCreateMenu = Settings::where('setting_type', 'shortcut_menus')->first();

        if ($settingCreateMenu) {
            $settingCreateMenu->credentials = $request->shortcut_menus_settings;
            $settingCreateMenu->save();
        }

        return ApiResponse::make('Success', []);
    }
}
