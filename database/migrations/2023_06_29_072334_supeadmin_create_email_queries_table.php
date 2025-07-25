<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SupeadminCreateEmailQueriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_queries', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_time');
            $table->string('name');
            $table->string('email');
            $table->text('message');
            $table->boolean('replied')->default(false);
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
        Schema::dropIfExists('email_queries');
    }
}
