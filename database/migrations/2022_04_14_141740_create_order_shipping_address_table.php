<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderShippingAddressTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_shipping_address', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('order_id')->unsigned();
			$table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade')->onUpdate('cascade');
			$table->string('name');
			$table->string('email');
			$table->string('phone');
			$table->string('address', 1000)->nullable()->default(null);
			$table->string('shipping_address', 1000)->nullable()->default(null);
			$table->string('city', 50)->nullable()->default(null);
			$table->string('state', 50)->nullable()->default(null);
			$table->string('country', 50)->nullable()->default(null);
			$table->string('zipcode', 50)->nullable()->default(null);
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
		Schema::dropIfExists('order_shipping_address');
	}
}
