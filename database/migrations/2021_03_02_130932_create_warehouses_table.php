<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable()->default(null);
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->boolean('show_email_on_invoice')->default(false);
            $table->boolean('show_phone_on_invoice')->default(false);
            $table->string('address')->nullable()->default(null);
            $table->text('terms_condition')->nullable()->default(null);
            $table->text('bank_details')->nullable()->default(null);
            $table->string('signature')->nullable()->default(null);
            $table->timestamps();
        });

        // Creating only for non-saas
        // if (app_type() == 'non-saas') {
        //     DB::table('warehouses')->insert([
        //         'name' => 'Stockifly',
        //         'email' => 'stockifly@example.com',
        //         'phone' => 9999999999,
        //         'terms_condition' => "1. Goods once sold will not be taken back or exchanged
        //         2. All disputes are subject to [ENTER_YOUR_CITY_NAME] jurisdiction only",
        //     ]);
        // }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouses');
    }
}
