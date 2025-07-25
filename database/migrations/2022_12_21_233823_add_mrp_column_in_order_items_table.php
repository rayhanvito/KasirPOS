<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddMrpColumnInOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // In the previous migration warehouse foreign key was wrong
        // Fixing it in this migration
        if (Schema::hasColumn('products', 'warehouse_id')) {
            $allProducts = DB::table('products')->select('warehouse_id', 'id')->get();

            Schema::table('products', function (Blueprint $table) {
                $table->dropForeign('products_warehouse_id_foreign');
                $table->dropColumn('warehouse_id');
            });

            Schema::table('products', function (Blueprint $table) {
                $table->bigInteger('warehouse_id')->unsigned()->nullable()->default(null)->after('company_id');
                $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('set null')->onUpdate('cascade');
            });

            foreach ($allProducts as $allProduct) {
                $waerhouse = DB::table('warehouses')
                    ->select('id')
                    ->where('id', $allProduct->warehouse_id)
                    ->first();

                if ($waerhouse) {
                    DB::table('products')
                        ->where('id', $allProduct->id)
                        ->update([
                            'warehouse_id' => $waerhouse->id
                        ]);
                }
            }
        } else {
            Schema::table('products', function (Blueprint $table) {
                $table->bigInteger('warehouse_id')->unsigned()->nullable()->default(null)->after('company_id');
                $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('set null')->onUpdate('cascade');
            });
        }

        Schema::table('order_items', function (Blueprint $table) {
            $table->double('mrp')->nullable()->default(null)->after('quantity');
        });

        Schema::table('warehouses', function (Blueprint $table) {
            $table->boolean('show_mrp_on_invoice')->default(true)->after('default_pos_order_status');
            $table->boolean('show_discount_tax_on_invoice')->default(true)->after('show_mrp_on_invoice');
        });

        $allOrderItems = DB::table('order_items')
            ->select('orders.warehouse_id', 'orders.from_warehouse_id', 'orders.order_type', 'order_items.id', 'order_items.product_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->get();

        foreach ($allOrderItems as $allOrderItem) {
            $warehouseId = $allOrderItem->order_type == 'stock-transfers' ? $allOrderItem->from_warehouse_id : $allOrderItem->warehouse_id;

            $productDetails = DB::table('product_details')
                ->select('product_details.mrp')
                ->where('product_details.product_id', '=', $allOrderItem->product_id)
                ->where('product_details.warehouse_id', '=', $warehouseId)
                ->first();

            if ($productDetails) {
                DB::table('order_items')
                    ->where('order_items.id', '=', $allOrderItem->id)
                    ->update([
                        'mrp' => $productDetails->mrp
                    ]);
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
        if (Schema::hasColumn('products', 'warehouse_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropForeign('products_warehouse_id_foreign');
                $table->dropColumn('warehouse_id');
            });
        }

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('mrp');
        });

        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropColumn('show_mrp_on_invoice');
            $table->dropColumn('show_discount_tax_on_invoice');
        });
    }
}
