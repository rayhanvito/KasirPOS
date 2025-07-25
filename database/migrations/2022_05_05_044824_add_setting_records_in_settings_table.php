<?php

use App\Classes\NotificationSeed;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class AddSettingRecordsInSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('mysqldump_command')->default("/usr/bin/mysqldump")->after('rtl');
        });

        if (app_type() == 'non-saas') {
            DB::table('settings')->insert([
                'setting_type' => 'shortcut_menus',
                'name' => 'Add Menu',
                'name_key' => 'shortcut_menus',
                'credentials' => json_encode([
                    'staff_member',
                    'customer',
                    'supplier',
                    'brand',
                    'category',
                    'product',
                    'purchase',
                    'sales',
                    'expense_category',
                    'expense',
                    'warehouse',
                    'currency',
                    'unit',
                    'language',
                    'role',
                    'tax',
                    'payment_mode',
                ]),
                'status' => 1,
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
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('mysqldump_command');
        });
    }
}
