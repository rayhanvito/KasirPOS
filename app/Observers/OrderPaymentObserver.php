<?php

namespace App\Observers;

use App\Models\OrderPayment;

class OrderPaymentObserver
{
    public function saving(OrderPayment $orderPayment)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $orderPayment->company_id = company()->id;
        }
    }
}
