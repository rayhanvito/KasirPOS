<?php

namespace App\Observers;

use App\Models\FrontProductCard;

class FrontProductCardObserver
{
    public function saving(FrontProductCard $frontProductCard)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $frontProductCard->company_id = company()->id;
        }
    }
}
