<?php

namespace App\Http\Middleware;

use App\Models\SubscriptionPlan;
use Carbon\Carbon;
use Closure;
use Examyou\RestAPI\Exceptions\ApiException;

class LicenseExpireDateWise
{

    public function handle($request, Closure $next)
    {
        if (app_type() == 'saas') {
            $company = company();
            $expireOn = $company->licence_expire_on;
            $currentDate = Carbon::now();
            $package = SubscriptionPlan::where('id', $company->subscription_plan_id)->first();

            if ((!is_null($expireOn) && $expireOn->lessThan($currentDate)) || ($company->status == 'license_expired'  && $package->default != 'yes')) {
                // If license column is not set license expired
                if ($company->status != 'license_expired') {
                    $company->status = 'license_expired';
                    $company->save();

                    company(true);
                }

                throw new ApiException('Plan expired', null, 405, 405);
            }
        }

        return $next($request);
    }
}
