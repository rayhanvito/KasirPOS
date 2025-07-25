<?php

namespace App\Observers;

use App\Models\WarehouseStock;

class WarehouseStockObserver
{
    public function saving(WarehouseStock $warehouseStock)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $warehouseStock->company_id = company()->id;
        }
    }
}
