<?php

use App\Classes\NotificationSeed;
use App\Models\Expense;
use App\Models\Payment;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeDateToDatetimeInExpensesAndPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dateTime('date')->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dateTime('date')->change();
        });

        // Bug Fix: Adding Email Templates For companies
        // If not exists
        $nonSaasCompanies = DB::table('companies')->select('id')->where('is_global', 0)->get();
        foreach ($nonSaasCompanies as $nonSaasCompany) {
            NotificationSeed::seedNotifications($nonSaasCompany->id);
        }

        $allExpenses = Expense::withoutGlobalScope(CompanyScope::class)->get();
        foreach ($allExpenses as $allExpense) {
            $company = DB::table('companies')->select('timezone')->where('id', '=', $allExpense->company_id)->first();

            $newDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $allExpense->date, $company->timezone)
                ->setTimezone('UTC')
                ->format('Y-m-d H:i:s');

            $allExpense->date = $newDateTime;
            $allExpense->save();
        }

        $allPayments = Payment::withoutGlobalScope(CompanyScope::class)->get();
        foreach ($allPayments as $allPayment) {
            $company = DB::table('companies')->select('timezone')->where('id', '=', $allPayment->company_id)->first();

            $newDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $allPayment->date, $company->timezone)
                ->setTimezone('UTC')
                ->format('Y-m-d H:i:s');

            $allPayment->date = $newDateTime;
            $allPayment->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $allExpenses = Expense::withoutGlobalScope(CompanyScope::class)->get();
        $allPayments = Payment::withoutGlobalScope(CompanyScope::class)->get();

        Schema::table('expenses', function (Blueprint $table) {
            $table->date('date')->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->date('date')->change();
        });

        foreach ($allExpenses as $allExpense) {
            $company = DB::table('companies')->select('timezone')->where('id', '=', $allExpense->company_id)->first();

            $newDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $allExpense->date, 'UTC')
                ->setTimezone($company->timezone)
                ->format('Y-m-d');

            $allExpense->date = $newDateTime;
            $allExpense->save();
        }

        foreach ($allPayments as $allPayment) {
            $company = DB::table('companies')->select('timezone')->where('id', '=', $allPayment->company_id)->first();

            $newDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $allPayment->date, 'UTC')
                ->setTimezone($company->timezone)
                ->format('Y-m-d');

            $allPayment->date = $newDateTime;
            $allPayment->save();
        }
    }
}
