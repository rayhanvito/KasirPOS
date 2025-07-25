<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->renameColumn('card_brand', 'pm_type');
            $table->renameColumn('card_last_four', 'pm_last_four');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('stripe_price')->nullable()->after('stripe_status');
        });

        Schema::table('subscription_items', function (Blueprint $table) {
            $table->renameColumn('stripe_plan', 'stripe_product');
            $table->string('stripe_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->renameColumn('pm_type', 'card_brand');
            $table->renameColumn('pm_last_four', 'card_last_four');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('stripe_price');
        });

        Schema::table('subscription_items', function (Blueprint $table) {
            $table->renameColumn('stripe_product', 'stripe_plan');
            $table->dropColumn('stripe_price');
        });
    }
};
