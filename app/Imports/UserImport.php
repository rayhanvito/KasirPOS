<?php

namespace App\Imports;

use App\Models\Role;
use App\Models\User;
use App\Models\UserWarehouse;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToArray;

class UserImport implements ToArray, WithHeadingRow
{

	public function array(array $users)
	{
		DB::transaction(function () use ($users) {
			$userType = "staff_members";
			$warehouse = warehouse();

			foreach ($users as $user) {
                $addRecord = true;

				if (
					!array_key_exists('name', $user) || !array_key_exists('email', $user) || !array_key_exists('phone', $user) || !array_key_exists('billing_address', $user) ||
					!array_key_exists('shipping_address', $user) || !array_key_exists('password', $user) || !array_key_exists('status', $user) || !array_key_exists('role', $user)
				) {
					throw new ApiException('Field missing from header.');
				}

				$roleName = trim($user['role']);
				$role = Role::where('display_name', $roleName)->first();
				if (!$role) {
					throw new ApiException('Role ' . $roleName . ' Not Exists');
				}

				$name = trim($user['name']);
				// $nameCount = User::withoutGlobalScope('type')->where('name', $name)->where('warehouse_id', $warehouse->id)->where('user_type', $userType)->count();
				// if ($nameCount > 0) {
				// 	throw new ApiException('User ' . $name . ' Already Exists');
				// }

				$email = trim($user['email']);
				$emailCount = User::withoutGlobalScope('type')->where('email', $email)->where('warehouse_id', $warehouse->id)->where('user_type', $userType)->count();
				if ($emailCount > 0) {
					if ($emailCount > 0) {
                        $addRecord = false;
                    }
				}

				$phone = trim($user['phone']);
				$phoneCount = User::withoutGlobalScope('type')->where('phone', $phone)->where('warehouse_id', $warehouse->id)->where('user_type', $userType)->count();
				if ($phoneCount > 0) {
					if ($phoneCount > 0) {
                        $addRecord = false;
                    }
				}

				$password = trim($user['password']);
				$address = trim($user['billing_address']);
				$shippingAddress = trim($user['shipping_address']);

				$status = trim($user['status']);
				if ($status != "" && !in_array($status, ['enabled', 'disabled'])) {
					throw new ApiException('Status must be enabled or disabled');
				}

                if($addRecord) {
                    $user = new User();
                    $user->role_id = $role->id;
                    $user->warehouse_id = $warehouse->id;
                    $user->user_type = $userType;
                    $user->name = $name;
                    $user->email = $email;
                    $user->phone = $phone;
                    $user->address = $address != "" ? $address : "";
                    $user->shipping_address = $shippingAddress != "" ? $shippingAddress : "";
                    $user->status = $status != "" ? $status : 'enabled';
                    $user->password = $password;
                    $user->save();

                    $userWarehouse = new UserWarehouse();
                    $userWarehouse->warehouse_id = $warehouse->id;
                    $userWarehouse->user_id = $user->id;
                    $userWarehouse->save();

                    $user->attachRole($user->role_id);
                }

			}
		});
	}
}
