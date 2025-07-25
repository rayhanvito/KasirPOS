<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxTypeColoumnInTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('taxes', function (Blueprint $table) {
            $table->bigInteger('parent_id')->unsigned()->nullable()->default(null)->after('id');
			$table->foreign('parent_id')->references('id')->on('taxes')->onDelete('cascade')->onUpdate('cascade');
            $table->string('tax_type',20)->nullable()->default('single')->after('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taxes', function (Blueprint $table) {
            $table->dropForeign('taxes_parent_id_foreign');
            $table->dropColumn('parent_id');
            $table->dropColumn('tax_type');
        });
    }
}
