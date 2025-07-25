<?php

namespace App\Observers;

use App\Models\Unit;

class UnitObserver
{
    public function saving(Unit $unit)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $unit->company_id = company()->id;
        }
    }
}
