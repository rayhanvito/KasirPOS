<?php

use App\Models\SubscriptionPlan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCompanyIdColumnsInAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->string('description', 1000)->nullable();
            $table->double('annual_price')->default(0);
            $table->double('monthly_price')->default(0);
            $table->integer('max_products')->unsigned()->default(0);
            $table->text('modules')->nullable()->default(null);
            $table->string('default', 20)->default('no'); // 'yes', 'no', 'trial'
            $table->boolean('is_popular')->default(0);
            $table->boolean('is_private')->default(0);
            $table->tinyInteger('billing_cycle')->nullable()->default(null);

            $table->string('stripe_monthly_plan_id')->nullable()->default(null);
            $table->string('stripe_annual_plan_id')->nullable()->default(null);
            $table->string('razorpay_monthly_plan_id')->nullable()->default(null);
            $table->string('razorpay_annual_plan_id')->nullable()->default(null);
            $table->string('paystack_monthly_plan_id')->nullable();
            $table->string('paystack_annual_plan_id')->nullable();

            $table->boolean('active')->default(1);
            $table->integer('duration')->nullable()->default(30);
            $table->integer('notify_before')->nullable()->default(null);
            $table->smallInteger('position')->nullable()->default(null);
            $table->text('features')->nullable()->default(null);
            $table->string('currency_code')->nullable()->default('USD');
            $table->string('currency_symbol')->nullable()->default('$');
            $table->timestamps();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->string('stripe_id')->nullable()->index();
            $table->string('card_brand')->nullable();
            $table->string('card_last_four', 4)->nullable();
            $table->timestamp('trial_ends_at')->nullable();

            $table->bigInteger('subscription_plan_id')->unsigned()->nullable()->default(null);
            $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans')->onDelete('set null')->onUpdate('cascade');
            $table->enum('package_type', ['monthly', 'annual'])->default('monthly');
            $table->date('licence_expire_on')->nullable()->default(null);

            $table->boolean('is_global')->default(false);
            $table->bigInteger('admin_id')->unsigned()->nullable()->default(null);
            $table->string('status')->default('active');
            $table->integer('total_users')->default(1);
            $table->string('email_verification_code')->nullable()->default(null);
            $table->boolean('verified')->default(false);
        });

        Schema::table('payment_modes', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('currencies', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('warehouses', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('is_superadmin')->default(false)->after('company_id');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('taxes', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('units', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('expense_categories', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('custom_fields', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('order_payments', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('warehouse_stocks', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('stock_history', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('is_global')->default(false)->after('id');
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('is_global');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('warehouse_history', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('order_shipping_address', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('user_address', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('front_product_cards', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('front_website_settings', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });

        $appEnv = env('APP_TYPE') ? env('APP_TYPE') : "non-saas";

        // Setting company id for non-saas version
        if ($appEnv == 'non-saas') {
            $company = DB::table('companies')->first();

            // Common::assignCompanyForNonSaas($company);
            // Assign Company for non-saas

            DB::table('payment_modes')->update(['company_id' => $company->id]);
            DB::table('currencies')->update(['company_id' => $company->id]);
            DB::table('warehouses')->update(['company_id' => $company->id]);
            DB::table('users')->update(['company_id' => $company->id]);
            DB::table('roles')->update(['company_id' => $company->id]);
            DB::table('brands')->update(['company_id' => $company->id]);
            DB::table('categories')->update(['company_id' => $company->id]);
            DB::table('products')->update(['company_id' => $company->id]);
            DB::table('taxes')->update(['company_id' => $company->id]);
            DB::table('units')->update(['company_id' => $company->id]);
            DB::table('expense_categories')->update(['company_id' => $company->id]);
            DB::table('expenses')->update(['company_id' => $company->id]);
            DB::table('custom_fields')->update(['company_id' => $company->id]);
            DB::table('orders')->update(['company_id' => $company->id]);
            DB::table('payments')->update(['company_id' => $company->id]);
            DB::table('order_payments')->update(['company_id' => $company->id]);
            DB::table('warehouse_stocks')->update(['company_id' => $company->id]);
            DB::table('stock_history')->update(['company_id' => $company->id]);
            DB::table('stock_adjustments')->update(['company_id' => $company->id]);
            DB::table('settings')->update(['company_id' => $company->id]);
            DB::table('warehouse_history')->update(['company_id' => $company->id]);
            DB::table('order_shipping_address')->update(['company_id' => $company->id]);
            DB::table('user_address')->update(['company_id' => $company->id]);
            DB::table('front_product_cards')->update(['company_id' => $company->id]);
            DB::table('front_website_settings')->update(['company_id' => $company->id]);
            DB::table('settings')->update(['company_id' => $company->id]);


            $adminRole = DB::table('roles')->where('name', 'admin')->first();
            if ($adminRole) {
                $firstAdmin = DB::table('users')->where('role_id', $adminRole->id)->first();
                DB::table('companies')->update([
                    'admin_id' => $firstAdmin->id
                ]);
            }

            // Insert records in settings table
            // For inital settings like email, storage
            // Common::insertInitSettings($company);
        }

        if ($appEnv == 'saas') {
            $defaultPlan = new SubscriptionPlan();
            $defaultPlan->name                    = 'Default';
            $defaultPlan->description             = 'Its a default package and cannot be deleted';
            $defaultPlan->annual_price            = 0;
            $defaultPlan->monthly_price           = 0;
            $defaultPlan->max_products            = 5;
            $defaultPlan->stripe_annual_plan_id   = 'default_plan';
            $defaultPlan->stripe_monthly_plan_id  = 'default_plan';
            $defaultPlan->default                 = 'yes';
            $defaultPlan->modules = [
                'stock_transfer',
                'stock_adjustment',
                'expense',
                'quotation',
            ];
            $defaultPlan->features = [];
            $defaultPlan->save();

            // Trail Subscription Plan
            $trailPlan = new SubscriptionPlan();
            $trailPlan->name                  = 'Trail';
            $trailPlan->description             = 'Its a trial package';
            $trailPlan->annual_price            = 0;
            $trailPlan->monthly_price           = 0;
            $trailPlan->max_products           = env('APP_ENV') == 'production' ? 200 : 5;
            $trailPlan->stripe_annual_plan_id   = 'trial_plan';
            $trailPlan->stripe_monthly_plan_id  = 'trial_plan';
            $trailPlan->default                 = 'trial';
            $trailPlan->modules = [
                'pos',
                'stock_transfer',
                'stock_adjustment',
                'online_store',
                'expense',
                'quotation',
                'purchase_return',
                'sales_return',
                'reports',
                'reports_download',
            ];
            $defaultPlan->features = [];
            $trailPlan->save();
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
            $table->dropForeign('companies_subscription_plan_id_foreign');
            $table->dropColumn([
                'stripe_id',
                'card_brand',
                'card_last_four',
                'trial_ends_at',
                'subscription_plan_id',
                'package_type',
                'is_global',
                'admin_id',
                'status',
                'total_users',
                'email_verification_code',
                'verified',
            ]);
        });

        Schema::dropIfExists('subscription_plans');

        // Payment Modes
        Schema::table('payment_modes', function (Blueprint $table) {
            $table->dropForeign('payment_modes_company_id_foreign');
            $table->dropColumn([
                'company_id'
            ]);
        });

        Schema::table('currencies', function (Blueprint $table) {
            $table->dropForeign('currencies_company_id_foreign');
            $table->dropColumn([
                'company_id'
            ]);
        });

        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropForeign('warehouses_company_id_foreign');
            $table->dropColumn([
                'company_id'
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_company_id_foreign');
            $table->dropColumn([
                'company_id'
            ]);
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropForeign('roles_company_id_foreign');
            $table->dropColumn([
                'company_id'
            ]);
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->dropForeign('brands_company_id_foreign');
            $table->dropColumn([
                'company_id'
            ]);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign('categories_company_id_foreign');
            $table->dropColumn([
                'company_id'
            ]);
        });

        Schema::table('taxes', function (Blueprint $table) {
            $table->dropForeign('taxes_company_id_foreign');
            $table->dropColumn([
                'company_id'
            ]);
        });

        Schema::table('units', function (Blueprint $table) {
            $table->dropForeign('units_company_id_foreign');
            $table->dropColumn([
                'company_id'
            ]);
        });

        Schema::table('expense_categories', function (Blueprint $table) {
            $table->dropForeign('expense_categories_company_id_foreign');
            $table->dropColumn([
                'company_id'
            ]);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign('expenses_company_id_foreign');
            $table->dropColumn([
                'company_id'
            ]);
        });

        Schema::table('custom_fields', function (Blueprint $table) {
            $table->dropForeign('custom_fields_company_id_foreign');
            $table->dropColumn([
                'company_id'
            ]);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_company_id_foreign');
            $table->dropColumn([
                'company_id'
            ]);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_company_id_foreign');
            $table->dropColumn([
                'company_id'
            ]);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign('payments_company_id_foreign');
            $table->dropColumn([
                'company_id'
            ]);
        });

        Schema::table('order_payments', function (Blueprint $table) {
            $table->dropForeign('order_payments_company_id_foreign');
            $table->dropColumn([
                'company_id'
            ]);
        });

        Schema::table('warehouse_stocks', function (Blueprint $table) {
            $table->dropForeign('warehouse_stocks_company_id_foreign');
            $table->dropColumn([
                'company_id'
            ]);
        });

        Schema::table('stock_history', function (Blueprint $table) {
            $table->dropForeign('stock_history_company_id_foreign');
            $table->dropColumn([
                'company_id'
            ]);
        });

        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropForeign('stock_adjustments_company_id_foreign');
            $table->dropColumn([
                'company_id'
            ]);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropForeign('settings_company_id_foreign');
            $table->dropColumn([
                'is_global',
                'company_id'
            ]);
        });

        Schema::table('warehouse_history', function (Blueprint $table) {
            $table->dropForeign('warehouse_history_company_id_foreign');
            $table->dropColumn([
                'company_id'
            ]);
        });

        Schema::table('order_shipping_address', function (Blueprint $table) {
            $table->dropForeign('order_shipping_address_company_id_foreign');
            $table->dropColumn([
                'company_id'
            ]);
        });

        Schema::table('user_address', function (Blueprint $table) {
            $table->dropForeign('user_address_company_id_foreign');
            $table->dropColumn([
                'company_id'
            ]);
        });

        Schema::table('front_product_cards', function (Blueprint $table) {
            $table->dropForeign('front_product_cards_company_id_foreign');
            $table->dropColumn([
                'company_id'
            ]);
        });

        Schema::table('front_website_settings', function (Blueprint $table) {
            $table->dropForeign('front_website_settings_company_id_foreign');
            $table->dropColumn([
                'company_id'
            ]);
        });
    }
}
