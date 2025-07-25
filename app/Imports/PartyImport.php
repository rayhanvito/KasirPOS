<?php

namespace App\Imports;

use App\Classes\Common;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\UserDetails;
use App\Models\Warehouse;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToArray;

class PartyImport implements ToArray, WithHeadingRow
{
    public $userType = "";

    public function __construct($userType)
    {
        $this->userType = $userType;
    }

    public function array(array $parties)
    {
        DB::transaction(function () use ($parties) {
            $user = user();
            $warehouse = warehouse();

            foreach ($parties as $party) {
                $addRecord = true;

                if (
                    !array_key_exists('name', $party) || !array_key_exists('email', $party) || !array_key_exists('phone', $party) || !array_key_exists('billing_address', $party) ||
                    !array_key_exists('shipping_address', $party) || !array_key_exists('opening_balance', $party) || !array_key_exists('opening_balance_type', $party) || !array_key_exists('credit_period', $party) ||
                    !array_key_exists('credit_limit', $party)
                ) {
                    throw new ApiException('Field missing from header.');
                }

                $name = trim($party['name']);
                // $nameCount = Customer::withoutGlobalScope('type')->where('name', $name)->where('user_type', $this->userType)->count();
                // if ($nameCount > 0) {
                //     throw new ApiException('User ' . $name . ' Already Exists');
                // }

                $email = trim($party['email']);
                if ($email != '') {
                    $emailCount = Customer::withoutGlobalScope('type')->where('email', $email)->where('user_type', $this->userType)->count();
                    if ($emailCount > 0) {
                        $addRecord = false;
                    }
                }


                $phone = trim($party['phone']);
                $phoneCount = Customer::withoutGlobalScope('type')->where('phone', $phone)->where('user_type', $this->userType)->count();
                if ($phoneCount > 0) {
                    $addRecord = false;
                }

                $address = trim($party['billing_address']);
                $shippingAddress = trim($party['shipping_address']);

                // Details
                $openingBalance = trim($party['opening_balance']);
                $openingBalanceType = strtolower(trim($party['opening_balance_type']));
                if ($openingBalance != "" && !in_array($openingBalanceType, ['pay', 'receive'])) {
                    throw new ApiException('Opening Balance Type must be pay or receive');
                }


                $creditPeriod = trim($party['credit_period']);
                $creditLimit = trim($party['credit_limit']);

                if($addRecord) {
                    if ($this->userType == "customers") {
                        $user = new Customer();
                    } else {
                        $user = new Supplier();
                    }
                    $user->user_type = $this->userType;
                    $user->name = $name;
                    $user->email = $email;
                    $user->phone = $phone;
                    $user->address = $address != "" ? $address : "";
                    $user->shipping_address = $shippingAddress != "" ? $shippingAddress : "";
                    $user->warehouse_id = $warehouse && $warehouse->id ? $warehouse->id : null;
                    $user->save();

                    $allWarehouses = Warehouse::select('id')->get();

                    foreach ($allWarehouses as $allWarehouse) {
                        $userDetails = new UserDetails();
                        $userDetails->warehouse_id = $allWarehouse->id;
                        $userDetails->user_id = $user->id;
                        $userDetails->opening_balance = $openingBalance == "" ? 0 : $openingBalance;
                        $userDetails->opening_balance_type = $openingBalanceType == "" ? 'receive' : $openingBalanceType;
                        $userDetails->credit_period = $creditPeriod == "" ? 30 : $creditPeriod;
                        $userDetails->credit_limit = $creditLimit == "" ? 0 : $creditLimit;
                        $userDetails->save();

                        Common::updateUserAmount($user->id, $allWarehouse->id);;
                    }
                }

            }
        });
    }
}
