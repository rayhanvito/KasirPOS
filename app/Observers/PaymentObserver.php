<?php

namespace App\Observers;

use App\Models\Payment;

class PaymentObserver
{
    public function saving(Payment $payment)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $payment->company_id = company()->id;
        }
    }
}
