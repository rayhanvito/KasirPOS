<?php

namespace App\Observers;

use App\Models\PaymentMode;

class PaymentModeObserver
{
    public function saving(PaymentMode $paymentMode)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $paymentMode->company_id = company()->id;
        }
    }
}
