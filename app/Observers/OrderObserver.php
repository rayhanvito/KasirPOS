<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    public function saving(Order $order)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $order->company_id = company()->id;
        }
    }
}
