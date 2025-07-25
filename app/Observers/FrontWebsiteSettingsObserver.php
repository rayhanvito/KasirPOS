<?php

namespace App\Observers;

use App\Models\FrontWebsiteSettings;

class FrontWebsiteSettingsObserver
{
    public function saving(FrontWebsiteSettings $frontWebsiteSettings)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $frontWebsiteSettings->company_id = company()->id;
        }
    }
}
