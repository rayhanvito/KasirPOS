<?php

namespace App\Observers;

use App\Models\CustomField;

class CustomFieldObserver
{
    public function saving(CustomField $customField)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $customField->company_id = company()->id;
        }
    }
}
