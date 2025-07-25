<?php

namespace App\Observers;

use App\Models\ExpenseCategory;

class ExpenseCategoryObserver
{
    public function saving(ExpenseCategory $expenseCategory)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $expenseCategory->company_id = company()->id;
        }
    }
}
