<?php

namespace App\Observers;

use App\Models\WarehouseHistory;

class WarehouseHistoryObserver
{
    public function saving(WarehouseHistory $warehouseHistory)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $warehouseHistory->company_id = company()->id;
        }
    }
}
