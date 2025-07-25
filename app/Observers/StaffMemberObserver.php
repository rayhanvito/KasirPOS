<?php

namespace App\Observers;

use App\Models\StaffMember;

class StaffMemberObserver
{
    public function saving(StaffMember $staffMember)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $staffMember->company_id = $company->id;
        }
    }
}
