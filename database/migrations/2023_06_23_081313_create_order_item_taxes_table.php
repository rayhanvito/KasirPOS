<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_item_taxes', function (Blueprint $table) {
            $table->id();
            $table->string('tax_name');
            $table->string('tax_type',20);
            $table->double('tax_amount');
            $table->float('tax_rate',8,2);
            $table->bigInteger('order_id')->unsigned();
			$table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('order_item_id')->unsigned();
			$table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('tax_id')->unsigned()->nullable();
			$table->foreign('tax_id')->references('id')->on('taxes')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_item_taxes');
    }
}
