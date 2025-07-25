<?php

use App\Classes\Common;
use App\Models\Lang;
use App\Models\Translation;
use App\SuperAdmin\Models\GlobalCompany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

Route::get('{path}', function () {
    if (file_exists(storage_path('installed'))) {

        // Front Store Warehouse
        $frontStoreDetails = Common::getStoreWarehouse();

        $appName = "StockiflySaas";
        $appVersion = File::get(public_path() . '/superadmin_version.txt');
        $modulesData = Common::moduleInformations();
        $themeMode = session()->has('theme_mode') ? session('theme_mode') : 'light';
        $company = GlobalCompany::first();
        $appVersion = File::get('superadmin_version.txt');
        $appVersion = preg_replace("/\r|\n/", "", $appVersion);
        $globalCompanyLang = DB::table('companies')->select('lang_id')->where('is_global', 1)->first();
        $lang = $globalCompanyLang && $globalCompanyLang->lang_id && $globalCompanyLang->lang_id != null ? Lang::find($globalCompanyLang->lang_id) : Lang::first();
        $loadingLangMessageLang = Translation::where('key', 'loading_app_message')
            ->where('group', 'messages')
            ->where('lang_id', $lang->id)
            ->first();

        return view('welcome', [
            'appName' => $appName,
            'appVersion' => preg_replace("/\r|\n/", "", $appVersion),
            'installedModules' => $modulesData['installed_modules'],
            'enabledModules' => $modulesData['enabled_modules'],
            'themeMode' => $themeMode,
            'company' => $company,
            'appVersion' => $appVersion,
            'appEnv' => env('APP_ENV'),
            'appType' => 'saas',
            'loadingLangMessageLang' => $loadingLangMessageLang->value,
            'frontStoreWarehouse' => $frontStoreDetails['warehouse'],
            'frontStoreCompany' => $frontStoreDetails['company'],
            'frontStoreSettings' => $frontStoreDetails['settings'],
            'loadingImage' => $frontStoreDetails['loadingImage'],
            'warehouseCurrency' => $frontStoreDetails['currency'],
            'defaultLangKey' => $lang->key
        ]);
    } else {
        return redirect('/install');
    }
})->where('path', '^(?!api.*$).*')->name('main');
