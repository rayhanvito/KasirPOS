<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCreatedByColumnInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `users` CHANGE COLUMN `email` `email` VARCHAR(191) NULL DEFAULT NULL;");

        Schema::table('users', function (Blueprint $table) {
            // $table->string('email')->nullable()->default(null)->change();

            $table->boolean('is_walkin_customer')->default(false)->after('user_type');
            $table->bigInteger('created_by')->unsigned()->nullable()->after('tax_number');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');

            $table->bigInteger('lang_id')->unsigned()->nullable()->after('role_id');
            $table->foreign('lang_id')->references('id')->on('langs')->onDelete('set null')->onUpdate('cascade');
        });

        Schema::table('warehouses', function (Blueprint $table) {
            $table->string('customers_visibility', 20)->default('all')->after('online_store_enabled'); // ['all', 'warehouse']
            $table->string('suppliers_visibility', 20)->default('all')->after('customers_visibility');
            $table->string('products_visibility', 20)->default('all')->after('suppliers_visibility');
            $table->string('default_pos_order_status', 20)->default('delivered')->after('products_visibility');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->bigInteger('warehouse_id')->unsigned()->nullable()->default(null)->after('company_id');
            $table->foreign('warehouse_id')->references('id')->on('products')->onDelete('set null')->onUpdate('cascade');
        });

        Schema::table('payment_modes', function (Blueprint $table) {
            $table->string('mode_type')->nullable()->default('bank')->after('name');
            $table->text('credentials')->nullable()->default(null)->after('mode_type');
        });

        // Setting name=Cash to  mode_type=>cash
        $allCompanies = DB::table('companies')->where('is_global', 0)->get();
        foreach ($allCompanies as $company) {
            $cashMode = DB::table('payment_modes')
                ->where('name', 'Cash')
                ->where('company_id', '=', $company->id)
                ->count();

            if ($cashMode > 0) {
                DB::table('payment_modes')
                    ->where('name', 'Cash')
                    ->where('company_id', '=', $company->id)
                    ->update([
                        'mode_type' => 'cash'
                    ]);
            }

            // Creating walk in customers
            $isalkinCustomerExists = DB::table('users')
                ->where('user_type', 'customers')
                ->where('is_walkin_customer', '=', 1)
                ->where('company_id', '=', $company->id)
                ->count();

            if ($isalkinCustomerExists == 0) {
                $customerId = DB::table('users')
                    ->insertGetId([
                        'company_id' => $company->id,
                        'login_enabled' => 0,
                        'name' => 'Walk In Customer',
                        'email' => 'walkin@email.com',
                        'phone' => '+911111111111',
                        'user_type' => 'customers',
                        'address' => 'address',
                        'shipping_address' => 'shipping address',
                        'is_walkin_customer' => 1,
                    ]);

                $allWarehouses = DB::table('warehouses')->select('id')->get();
                foreach ($allWarehouses as $allWarehouse) {
                    DB::table('user_details')->insert([
                        'warehouse_id' => $allWarehouse->id,
                        'user_id' => $customerId,
                        'credit_period' => 30,
                    ]);
                }
            }
        }

        // Setting All Users & All Companies lang_id to en
        $enLang = DB::table('langs')->where('key', 'en')->first();
        DB::table('users')->update(['lang_id' => $enLang->id]);
        DB::table('companies')->update(['lang_id' => $enLang->id]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('users')
            ->where('user_type', 'customers')
            ->where('is_walkin_customer', 1)
            ->delete();

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->change();
            $table->dropColumn('is_walkin_customer');
            $table->dropForeign('users_created_by_foreign');
            $table->dropColumn('created_by');
            $table->dropForeign('users_lang_id_foreign');
            $table->dropColumn('lang_id');
        });

        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropColumn('customers_visibility');
            $table->dropColumn('suppliers_visibility');
            $table->dropColumn('products_visibility');
            $table->dropColumn('default_pos_order_status');
        });

        if (Schema::hasColumn('products', 'warehouse_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('warehouse_id');
                $table->dropForeign('products_warehouse_id_foreign');
            });
        }

        Schema::table('payment_modes', function (Blueprint $table) {
            $table->dropColumn('mode_type');
            $table->dropColumn('credentials');
        });
    }
}
