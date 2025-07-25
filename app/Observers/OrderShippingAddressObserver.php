<?php

namespace App\Observers;

use App\Models\OrderShippingAddress;

class OrderShippingAddressObserver
{
    public function saving(OrderShippingAddress $orderShippingAddress)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $orderShippingAddress->company_id = company()->id;
        }
    }
}
