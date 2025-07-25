<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddFromWarehouseIdColumnInOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->bigInteger('from_warehouse_id')->unsigned()->nullable()->default(null)->after('warehouse_id');
            $table->foreign('from_warehouse_id')->references('id')->on('warehouses')->onDelete('cascade')->onUpdate('cascade');
        });

        DB::statement("ALTER TABLE `order_items` CHANGE COLUMN `user_id` `user_id` BIGINT UNSIGNED NULL DEFAULT NULL  COMMENT '' AFTER `id`;");


        if (app_type() == 'non-saas') {
            // Add front website settings for each warehouse
            $allWarehouses = DB::table('warehouses')->get();

            foreach ($allWarehouses as $allWarehouse) {

                $frontSetting = DB::table('front_website_settings')->where('warehouse_id', $allWarehouse->id)
                    ->first();

                if (!$frontSetting) {
                    DB::table('front_website_settings')->insert([
                        'warehouse_id' => $allWarehouse->id,
                        'featured_categories' => json_encode([]),
                        'featured_products' => json_encode([]),
                        'features_lists' => json_encode([]),
                        'pages_widget' => json_encode([]),
                        'contact_info_widget' => json_encode([]),
                        'links_widget' => json_encode([]),
                        'top_banners' => json_encode([]),
                        'bottom_banners_1' => json_encode([]),
                        'bottom_banners_2' => json_encode([]),
                        'bottom_banners_3' => json_encode([]),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_from_warehouse_id_foreign');
            $table->dropColumn('from_warehouse_id');
        });
    }
}
