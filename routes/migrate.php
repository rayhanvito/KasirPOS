<?php


use App\Classes\LangTrans;
use App\Classes\PermsSeed;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::get('/{path?}', function () {

    // For Running Migration
    PermsSeed::seedMainPermissions();
    LangTrans::seedMainTranslations();
    Artisan::call('migrate', ['--force' => true]);

    // Config clear
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');

    // Deleting migrate file
    File::delete(public_path() . '/migrate');

    return redirect('/');
})->where('path', '.*')->name('migrate');
