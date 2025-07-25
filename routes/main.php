<?php

use App\Classes\Common;
use App\Models\Company;
use App\Models\Lang;
use App\Models\Translation;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::get('{path}', function () {
    if (file_exists(storage_path('installed'))) {

        // Front Store Warehouse
        $frontStoreDetails = Common::getStoreWarehouse();

        $appName = "Stockifly";
        $appVersion = File::get(public_path() . '/version.txt');
        $modulesData = Common::moduleInformations();
        $themeMode = session()->has('theme_mode') ? session('theme_mode') : 'light';
        $company = Company::first();
        $appVersion = File::get('version.txt');
        $appVersion = preg_replace("/\r|\n/", "", $appVersion);
        $lang = $company && $company->lang_id && $company->lang_id != null ? Lang::find($company->lang_id) : Lang::first();
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
            'appType' => 'non-saas',
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
