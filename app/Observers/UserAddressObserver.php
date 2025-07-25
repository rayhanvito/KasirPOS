<?php

namespace App\Observers;

use App\Models\UserAddress;

class UserAddressObserver
{
    public function saving(UserAddress $userAddress)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $userAddress->company_id = company()->id;
        }
    }
}
