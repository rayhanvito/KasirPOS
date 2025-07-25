<?php

namespace App\Observers;

use App\Models\Expense;

class ExpenseObserver
{
    public function saving(Expense $expense)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $expense->company_id = company()->id;
        }
    }
}
