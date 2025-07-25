<?php

namespace Database\Seeders;

use App\Classes\Common;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StockAdjustmentTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */

	public function run()
	{
		Model::unguard();

		DB::table('stock_adjustments')->delete();

		DB::statement('ALTER TABLE stock_adjustments AUTO_INCREMENT = 1');

		$faker = \Faker\Factory::create();
		$warehouses = Warehouse::select('id')->get();

		$takeRecordsNumber = $faker->numberBetween(30, 50);
		$products = Product::select('id')->inRandomOrder()->take($takeRecordsNumber)->get();

		foreach ($warehouses as $warehouse) {
			foreach ($products as $product) {
				$randomUser = User::select('id')->inRandomOrder()->where('status', 'enabled')->first();

				if ($randomUser) {
					$newStockAdjustment = new StockAdjustment();
					$newStockAdjustment->warehouse_id = $warehouse->id;
					$newStockAdjustment->product_id = $product->id;
					$newStockAdjustment->quantity = $faker->randomNumber(2, false);
					$newStockAdjustment->created_by = $randomUser->id;
					$newStockAdjustment->save();

					Common::recalculateOrderStock($newStockAdjustment->warehouse_id, $newStockAdjustment->product_id);
				}
			}
		}
	}
}
