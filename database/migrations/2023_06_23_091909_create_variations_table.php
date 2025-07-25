<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->string('name',20);
            $table->bigInteger('parent_id')->unsigned()->nullable();
            $table->foreign('parent_id')->references('id')->on('variations')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('product_type', 10)->default('single')->after('warehouse_id'); // ['single','variable','combo']
            $table->bigInteger('parent_id')->unsigned()->nullable()->default(null)->after('product_type');
            $table->foreign('parent_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');
            $table->string('parent_item_code')->nullable()->default(null)->after('parent_id');
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('variant_id')->unsigned()->nullable()->default(null);
            $table->foreign('variant_id')->references('id')->on('variations')->onDelete('set null')->onUpdate('cascade');
            $table->bigInteger('variant_value_id')->unsigned()->nullable()->default(null);
            $table->foreign('variant_value_id')->references('id')->on('variations')->onDelete('set null')->onUpdate('cascade');
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
        Schema::dropIfExists('product_variants');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('parent_item_code');
            $table->dropColumn('product_type');
            $table->dropForeign('products_parent_id_foreign');
            $table->dropColumn(['parent_id']);
        });


        Schema::dropIfExists('variations');
    }
}
