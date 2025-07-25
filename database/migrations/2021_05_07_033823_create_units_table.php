<?php

use App\Classes\Common;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name');
            $table->string('base_unit')->nullable()->default(null);
            $table->bigInteger('parent_id')->unsigned()->nullable()->default(null);
            $table->foreign('parent_id')->references('id')->on('units')->onDelete('cascade')->onUpdate('cascade');
            $table->string('operator');
            $table->string('operator_value');
            $table->boolean('is_deletable')->default(true);
            $table->timestamps();
        });

        // if (app_type() == 'non-saas') {
        //     $allUnits = Common::allUnits();

        //     foreach ($allUnits as $allUnit) {
        //         DB::table('units')->insert([
        //             'name' => $allUnit['name'],
        //             'short_name' => $allUnit['short_name'],
        //             'operator' => $allUnit['operator'],
        //             'operator_value' => $allUnit['operator_value'],
        //             'is_deletable' => false,
        //         ]);
        //     }
        // }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('units');
    }
}
