<?php

namespace App\Observers;

use App\Models\Variation;

class VariationObserver
{
    public function saving(Variation $variation)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $variation->company_id = company()->id;
        }
    }
}
