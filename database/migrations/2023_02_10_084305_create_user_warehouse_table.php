<?php

use App\Models\User;
use App\Models\UserWarehouse;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUserWarehouseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_warehouse', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('warehouse_id')->unsigned();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });

        $allUsers = User::select('id', 'warehouse_id')
            ->where('user_type', 'staff_members')
            ->whereNotNull('warehouse_id')
            ->get();

        foreach ($allUsers as $allUser) {
            $userWarehouse = new UserWarehouse();
            $userWarehouse->user_id = $allUser->id;
            $userWarehouse->warehouse_id = $allUser->warehouse_id;
            $userWarehouse->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_warehouse');
    }
}
