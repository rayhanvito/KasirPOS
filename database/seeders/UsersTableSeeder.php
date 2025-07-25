<?php

namespace Database\Seeders;

use App\Classes\Common;
use App\Models\Customer;
use App\Models\Role;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\UserWarehouse;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Model::unguard();

        DB::table('users')->delete();
        DB::table('user_details')->delete();
        DB::table('role_user')->delete();
        DB::table('user_warehouse')->delete();

        DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE user_details AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE role_user AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE user_warehouse AUTO_INCREMENT = 1');

        $count = env('SEED_RECORD_COUNT', 30);
        $faker = \Faker\Factory::create();
        $electroniflyWarehouse = Warehouse::where('email', 'electronifly@example.com')->first();


        // Admin User
        $adminRole = Role::where('name', 'admin')->first();
        $admin = new User();
        $admin->name = 'Admin';
        $admin->email = 'admin@example.com';
        $admin->password = '12345678';
        $admin->warehouse_id = $electroniflyWarehouse->id;
        $admin->role_id = $adminRole->id;
        $admin->user_type = "staff_members";
        $admin->save();
        $admin->attachRole($adminRole->id);

        // Salesman
        $salesmanRole = Role::where('name', 'salesman')->first();
        $salesman = new User();
        $salesman->name = 'Salesman';
        $salesman->email = 'salesman@example.com';
        $salesman->password = '12345678';
        $salesman->warehouse_id = $electroniflyWarehouse->id;
        $salesman->role_id = $salesmanRole->id;
        $salesman->user_type = "staff_members";
        $salesman->save();
        $salesman->attachRole($salesmanRole->id);

        $userWarehouse = new UserWarehouse();
        $userWarehouse->user_id = $salesman->id;
        $userWarehouse->warehouse_id = $electroniflyWarehouse->id;
        $userWarehouse->save();

        // Stock Manager
        $stockManagerRole = Role::where('name', 'stock_manager')->first();
        $stockManager = new User();
        $stockManager->name = 'Stock Manager';
        $stockManager->email = 'stockmanager@example.com';
        $stockManager->password = '12345678';
        $stockManager->warehouse_id = $electroniflyWarehouse->id;
        $stockManager->role_id = $stockManagerRole->id;
        $stockManager->user_type = "staff_members";
        $stockManager->save();
        $stockManager->attachRole($stockManagerRole->id);

        $userWarehouse = new UserWarehouse();
        $userWarehouse->user_id = $stockManager->id;
        $userWarehouse->warehouse_id = $electroniflyWarehouse->id;
        $userWarehouse->save();

        $allWarehouses = Warehouse::select('id')->pluck('id');

        // StaffMembers
        User::factory()->count((int)$count)->create()->each(function ($user) use ($faker, $adminRole, $salesmanRole, $stockManagerRole, $allWarehouses) {

            $roleId = $faker->randomElement([$adminRole->id, $salesmanRole->id, $stockManagerRole->id]);

            $user->role_id = $roleId;
            $user->warehouse_id = $faker->randomElement($allWarehouses);
            $user->save();

            $userWarehouse = new UserWarehouse();
            $userWarehouse->user_id = $user->id;
            $userWarehouse->warehouse_id = $user->warehouse_id;
            $userWarehouse->save();

            // TODO - Add more warehouse

            // Assign Role
            $user->attachRole($roleId);
        });

        // Customers
        Customer::factory()->count((int)$count)->create([
            'warehouse_id' => $electroniflyWarehouse->id
        ])->each(function ($user) use ($faker, $allWarehouses) {
            foreach ($allWarehouses as $allWarehouse) {
                UserDetails::factory()->create([
                    'warehouse_id' => $allWarehouse,
                    'user_id' => $user->id,
                ]);

                Common::updateUserAmount($user->id, $allWarehouse);
            }
        });

        // Suppliers
        Supplier::factory()->count((int)$count)->create([
            'warehouse_id' => $electroniflyWarehouse->id
        ])->each(function ($user) use ($faker, $allWarehouses) {
            foreach ($allWarehouses as $allWarehouse) {
                UserDetails::factory()->create([
                    'warehouse_id' => $allWarehouse,
                    'user_id' => $user->id,
                ]);

                Common::updateUserAmount($user->id, $allWarehouse);
            }
        });
    }
}
