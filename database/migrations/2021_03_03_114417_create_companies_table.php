<?php

use App\Models\Currency;
use App\Models\Lang;
use App\Models\Warehouse;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable()->default(NULL);
            $table->string('email')->nullable()->default(NULL);
            $table->string('phone')->nullable()->default(NULL);
            $table->string('website')->nullable()->default(NULL);
            $table->string('light_logo')->nullable()->default(NULL);
            $table->string('dark_logo')->nullable()->default(NULL);
            $table->string('small_dark_logo')->nullable()->default(NULL);
            $table->string('small_light_logo')->nullable()->default(NULL);
            $table->string('address', 1000)->nullable()->default(NULL);
            $table->string('app_layout', 10)->default('sidebar');
            $table->bigInteger('currency_id')->unsigned()->nullable();
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null')->onUpdate('cascade');
            $table->bigInteger('lang_id')->unsigned()->nullable();
            $table->foreign('lang_id')->references('id')->on('langs')->onDelete('set null')->onUpdate('cascade');
            $table->bigInteger('warehouse_id')->unsigned()->nullable();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('set null')->onUpdate('cascade');
            $table->string('left_sidebar_theme', 20)->default("dark");
            $table->string('primary_color', 20)->default("#1890ff");
            $table->string('date_format', 20)->default("DD-MM-YYYY");
            $table->string('time_format', 20)->default("hh:mm a");
            $table->boolean('auto_detect_timezone')->default(true); // Allow Browser To Auto Detect timezone For Logged In User
            $table->string('timezone')->default("Asia/Kolkata");
            $table->string('session_driver', 20)->default("file");
            $table->boolean('app_debug')->default(false);
            $table->boolean('update_app_notification')->default(true);
            $table->timestamps();
        });

        if (app_type() == 'non-saas') {

            // $warehouse = Warehouse::withoutGlobalScope(CompanyScope::class)->first();
            // $currency = Currency::withoutGlobalScope(CompanyScope::class)->where('code', "USD")->first();
            // $enLang = Lang::where('key', 'en')->first();

            // Creating entries using DB
            // So that no observer will be called
            DB::table('companies')->insert([
                'name' => 'Stockifly',
                'short_name' => 'Stockifly',
                'email' => 'company@example.com',
                'phone' => '+9199999999',
                'address' => '7 street, city, state, 762782',
                // 'currency_id' => $currency->id,
                // 'warehouse_id' => $warehouse->id,
                // 'lang_id' => $enLang->id,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
