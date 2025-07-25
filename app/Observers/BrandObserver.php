<?php

namespace App\Observers;

use App\Models\Brand;

class BrandObserver
{
    public function saving(Brand $brand)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $brand->company_id = company()->id;
        }
    }
}
