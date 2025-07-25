<?php

namespace App\Observers;

use App\Models\StockHistory;

class StockHistoryObserver
{
    public function saving(StockHistory $stockHistory)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $stockHistory->company_id = company()->id;
        }
    }
}
