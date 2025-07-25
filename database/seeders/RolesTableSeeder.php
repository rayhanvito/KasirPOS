<?php

namespace Database\Seeders;

use App\Classes\PermsSeed;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */

	public function run()
	{
		Model::unguard();

		DB::table('permissions')->delete();
		DB::table('roles')->delete();
		DB::table('permission_role')->delete();

		DB::statement('ALTER TABLE `permissions` AUTO_INCREMENT = 1');
		DB::statement('ALTER TABLE roles AUTO_INCREMENT = 1');
		DB::statement('ALTER TABLE permission_role AUTO_INCREMENT = 1');

		PermsSeed::seedMainPermissions();

		$adminRole = new Role();
		$adminRole->name = 'admin';
		$adminRole->display_name = 'Admin';
		$adminRole->description = 'Admin is allowed to manage everything of the app.';
		$adminRole->save();

		$salesmaneRole = new Role();
		$salesmaneRole->name = 'salesman';
		$salesmaneRole->display_name = 'Salesman';
		$salesmaneRole->description = 'Salesman is allowed to manage everything related to sales.';
		$salesmaneRole->save();

		$salesmanePermissions = Permission::whereIn('name', [
			'sales_view', 'sales_create', 'sales_edit',
			'sales_returns_view', 'sales_returns_create', 'sales_returns_edit',
			'order_payments_view', 'order_payments_create',
			'payment_in_view', 'payment_in_create', 'payment_in_edit',
			'expense_categories_view', 'expense_categories_create', 'expense_categories_edit',
			'expenses_view', 'expenses_create', 'expenses_edit',
			'customers_view', 'customers_create', 'customers_edit',
		])->pluck('id');
		$salesmaneRole->savePermissions($salesmanePermissions);

		$stockManagerRole = new Role();
		$stockManagerRole->name = 'stock_manager';
		$stockManagerRole->display_name = 'Stock Manager';
		$stockManagerRole->description = 'Stock Manager is allowed to manage everything related to stocks.';
		$stockManagerRole->save();

		$stockManagerPermissions = Permission::whereIn('name', [
			'purchases_view', 'purchases_create', 'purchases_edit',
			'purchase_returns_view', 'purchase_returns_create', 'purchase_returns_edit',
			'order_payments_view', 'order_payments_create',
			'payment_out_view', 'payment_out_create', 'payment_out_edit',
			'expense_categories_view', 'expense_categories_create', 'expense_categories_edit',
			'expenses_view', 'expenses_create', 'expenses_edit',
			'suppliers_view', 'suppliers_create', 'suppliers_edit',
		])->pluck('id');
		$stockManagerRole->savePermissions($stockManagerPermissions);
	}
}
