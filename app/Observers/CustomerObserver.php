<?php

namespace App\Observers;

use App\Classes\Common;
use App\Models\Customer;
use Illuminate\Support\Facades\File;

class CustomerObserver
{
    public function saving(Customer $customer)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $customer->company_id = $company->id;
        }
    }

    public function updating(Customer $user)
    {
        $original = $user->getOriginal();
        if ($user->isDirty('image')) {
            $userImagePath = Common::getFolderPath('userImagePath');

            File::delete($userImagePath . $original['image']);
        }
    }

    public function deleting(Customer $user)
    {
        $userImagePath = Common::getFolderPath('userImagePath');

        File::delete($userImagePath . $user->image);
    }
}
