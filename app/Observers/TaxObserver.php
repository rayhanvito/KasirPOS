<?php

namespace App\Observers;

use App\Models\Tax;

class TaxObserver
{
    public function saving(Tax $tax)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $tax->company_id = company()->id;
        }
    }
}
