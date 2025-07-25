<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRtlInCompaniesWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('rtl')->default(false)->after('app_layout');
            $table->string('shortcut_menus', 20)->default('top_bottom')->after('rtl'); // top, bottom, top_bottom
        });

        // Option to enable disable online store
        Schema::table('warehouses', function (Blueprint $table) {
            $table->boolean('online_store_enabled')->default(true)->after('signature');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('rtl');
            $table->dropColumn('shortcut_menus');
        });

        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropColumn('online_store_enabled');
        });
    }
}
