<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->string('symbol');
            $table->string('position');
            $table->boolean('is_deletable')->default(true);
            $table->timestamps();
        });

        // Creating only for non-saas
        // if (app_type() == 'non-saas') {
        //     DB::table('currencies')->insert([
        //         [
        //             'name' => 'Dollar',
        //             'code' => 'USD',
        //             'symbol' => '$',
        //             'position' => 'front',
        //             'is_deletable' => false,
        //         ],
        //         [
        //             'name' => 'Rupee',
        //             'code' => 'INR',
        //             'symbol' => '₹',
        //             'position' => 'front',
        //             'is_deletable' => false,
        //         ],
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
        Schema::dropIfExists('currencies');
    }
}
