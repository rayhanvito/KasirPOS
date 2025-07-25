<?php

namespace App\Observers;

use App\Classes\Common;
use App\Models\Supplier;
use Illuminate\Support\Facades\File;

class SupplierObserver
{
    public function saving(Supplier $supplier)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $supplier->company_id = $company->id;
        }
    }

    public function updating(Supplier $user)
    {
        $original = $user->getOriginal();
        if ($user->isDirty('image')) {
            $userImagePath = Common::getFolderPath('userImagePath');

            File::delete($userImagePath . $original['image']);
        }
    }

    public function deleting(Supplier $user)
    {
        $userImagePath = Common::getFolderPath('userImagePath');

        File::delete($userImagePath . $user->image);
    }
}
