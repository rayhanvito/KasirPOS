<?php

namespace App\SuperAdmin\Observers;

use App\Classes\Common;
use App\Models\Company;
use App\Models\FrontWebsiteSettings;
use App\Models\PaymentMode;
use App\Models\Role;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CompanyObserver
{

    public function created(Company $company)
    {
        // $company = Common::addCurrencies($company);

        if (!$company->is_global) {
            $company = $this->addAdminRole($company);
            Common::insertInitSettings($company);
            $company = Common::createCompanyWalkInCustomer($company);

            // Adding Default Subscription Plan
            $company =  $this->addInitialSubscriptionPlan($company);
        }
    }

    public function addAdminRole($company)
    {
        // Seeding Data
        $adminRole = new Role();
        $adminRole->company_id = $company->id;
        $adminRole->name = 'admin';
        $adminRole->display_name = 'Admin';
        $adminRole->description = 'Admin is allowed to manage everything of the app.';
        $adminRole->save();

        return $company;
    }

    public function addPaymentMode($company)
    {
        $paymentMode = new PaymentMode();
        $paymentMode->company_id = $company->id;
        $paymentMode->name = "Cash";
        $paymentMode->save();

        return $company;
    }

    public function addWarehouse($company)
    {
        $warehouse = new Warehouse();
        $warehouse->company_id = $company->id;
        $warehouse->name = $company->name . "Warehouse";
        $warehouse->slug = Str::slug($company->name . "Warehouse");
        $warehouse->email = "warehouse@example.com";
        $warehouse->phone = 9999999999;
        $warehouse->terms_condition = "1. Goods once sold will not be taken back or exchanged
2. All disputes are subject to [ENTER_YOUR_CITY_NAME] jurisdiction only";
        $warehouse->save();

        $frontSetting = new FrontWebsiteSettings();
        $frontSetting->company_id = $company->id;
        $frontSetting->warehouse_id = $warehouse->id;
        $frontSetting->featured_categories = [];
        $frontSetting->featured_products = [];
        $frontSetting->features_lists = [];
        $frontSetting->pages_widget = [];
        $frontSetting->contact_info_widget = [];
        $frontSetting->links_widget = [];
        $frontSetting->top_banners = [];
        $frontSetting->bottom_banners_1 = [];
        $frontSetting->bottom_banners_2 = [];
        $frontSetting->bottom_banners_3 = [];
        $frontSetting->footer_company_description = $warehouse->name . " have many propular products wiht high discount and special offers.";
        $frontSetting->footer_copyright_text =  "Copyright " . date("Y") . " @ " . $warehouse->name . ", All rights reserved.";
        $frontSetting->save();

        $company->warehouse_id = $warehouse->id;
        $company->save();

        return $company;
    }

    public function addTax($company)
    {
        $tax = new Tax();
        $tax->company_id = $company->id;
        $tax->name = "GST";
        $tax->rate = 18;
        $tax->save();

        return $company;
    }

    public function addUnits($company)
    {
        $allUnits = Common::allUnits();

        foreach ($allUnits as $allUnit) {
            $unit = new Unit();
            $unit->company_id = $company->id;
            $unit->name = $allUnit['name'];
            $unit->short_name = $allUnit['short_name'];
            $unit->operator = $allUnit['operator'];
            $unit->operator_value = $allUnit['operator_value'];
            $unit->is_deletable = false;
            $unit->save();
        }

        return $company;
    }

    public function addInitialSubscriptionPlan($company)
    {
        // Adding trial or default plan as initial plan
        if (app_type() == 'saas') {

            $trialPlan = SubscriptionPlan::where('default', 'trial')->first();
            $defaultPlan = SubscriptionPlan::where('default', 'yes')->first();

            // if trial package is active set package to company
            if ($trialPlan && $trialPlan->active == 1) {
                $company->subscription_plan_id = $trialPlan->id;

                // set company license expire date
                $company->licence_expire_on = Carbon::now()->addDays($trialPlan->duration)->format('Y-m-d');
            }
            // if trial package is not active set default package to company
            else {
                $company->subscription_plan_id = $defaultPlan->id;
                $company->licence_expire_on = null;
                $company->status = 'license_expired';
            }

            $company->save();
        }

        return $company;
    }
}
