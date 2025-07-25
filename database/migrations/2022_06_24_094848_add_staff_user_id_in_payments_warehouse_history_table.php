<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddStaffUserIdInPaymentsWarehouseHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->bigInteger('staff_user_id')->unsigned()->nullable()->after('notes');
            $table->foreign('staff_user_id')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
        });
        Schema::table('warehouse_history', function (Blueprint $table) {
            $table->bigInteger('staff_user_id')->unsigned()->nullable()->after('transaction_number');
            $table->foreign('staff_user_id')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
        });

        if (app_type() == 'non-saas') {
            $adminRole = DB::table('roles')->where('name', 'admin')->first();

            if ($adminRole) {
                $firstAdmin = DB::table('users')->where('role_id', $adminRole->id)->first();

                if ($firstAdmin) {
                    $allPayments = DB::table('payments')->get();

                    foreach ($allPayments as $allPayment) {
                        DB::table('payments')->where('id', $allPayment->id)->update([
                            'staff_user_id' => $firstAdmin->id,
                        ]);
                    }

                    $allWarehouseHistories = DB::table('warehouse_history')->get();

                    foreach ($allWarehouseHistories as $allWarehouseHistory) {
                        if ($allWarehouseHistory->order_id != null) {
                            $order = DB::table('orders')->select('staff_user_id')->where('id', $allWarehouseHistory->order_id)->first();
                            DB::table('warehouse_history')->where('id', $allWarehouseHistory->id)->update([
                                'staff_user_id' => $order->staff_user_id,
                            ]);
                        } else if ($allWarehouseHistory->payment_id != null) {
                            DB::table('warehouse_history')->where('id', $allWarehouseHistory->id)->update([
                                'staff_user_id' => $firstAdmin->staff_user_id,
                            ]);
                        } else if ($allWarehouseHistory->expense_id != null) {
                            $expense = DB::table('expenses')->where('id', $allWarehouseHistory->expense_id)->first();
                            DB::table('warehouse_history')->where('id', $allWarehouseHistory->id)->update([
                                'staff_user_id' => $expense->user_id,
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign('payments_staff_user_id_foreign');
            $table->dropColumn('staff_user_id');
        });

        Schema::table('warehouse_history', function (Blueprint $table) {
            $table->dropForeign('warehouse_history_staff_user_id_foreign');
            $table->dropColumn('staff_user_id');
        });
    }
}
