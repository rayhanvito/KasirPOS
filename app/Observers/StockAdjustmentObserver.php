<?php

namespace App\Observers;

use App\Models\StockAdjustment;

class StockAdjustmentObserver
{
    public function saving(StockAdjustment $stockAdjustment)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $stockAdjustment->company_id = company()->id;
        }
    }
}
