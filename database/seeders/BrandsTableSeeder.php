<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BrandsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */

	public function run()
	{
		Model::unguard();

		DB::table('brands')->delete();

		DB::statement('ALTER TABLE brands AUTO_INCREMENT = 1');

		$brands = [
			[
				"name" => "Sony",
				"slug" => "sony",
			],
			[
				"name" => "Apple",
				"slug" => "apple",
			],
			[
				"name" => "Zebronics",
				"slug" => "zebronics",
			],
			[
				"name" => "Lenova",
				"slug" => "lenova",
			],
			[
				"name" => "Asus",
				"slug" => "asus",
			],
			[
				"name" => "Dell",
				"slug" => "dell",
			],
			[
				"name" => "Samsung",
				"slug" => "samsung",
			],
			[
				"name" => "Oppo",
				"slug" => "oppo",
			],
			[
				"name" => "Acer",
				"slug" => "acer",
			],
			[
				"name" => "Gildan",
				"slug" => "gildan",
			],
			[
				"name" => "Lee",
				"slug" => "lee",
			],
			[
				"name" => "Champion",
				"slug" => "champion",
			],
			[
				"name" => "Adidas",
				"slug" => "adidas",
			],
			[
				"name" => "Puma",
				"slug" => "puma",
			],
			[
				"name" => "Reebok",
				"slug" => "reebok",
			],
			[
				"name" => "Lee Cooper",
				"slug" => "lee-cooper",
			],
			[
				"name" => "Glad",
				"slug" => "glad",
			],
			[
				"name" => "Maldon",
				"slug" => "maldon",
			],
			[
				"name" => "Welch's",
				"slug" => "welch's",
			],
			[
				"name" => "Peet's",
				"slug" => "peet's",
			],
			[
				"name" => "Tostitos",
				"slug" => "tostitos",
			],
			[
				"name" => "Grandma's",
				"slug" => "grandma's",
			],
			[
				"name" => "Pampers",
				"slug" => "pampers",
			],
			[
				"name" => "Infantino",
				"slug" => "infantino",
			],
			[
				"name" => "Graco",
				"slug" => "graco",
			],
			[
				"name" => "Munchkin",
				"slug" => "munchkin",
			],
			[
				"name" => "Zinus",
				"slug" => "zinus",
			],
			[
				"name" => "Furinno",
				"slug" => "furinno",
			],
			[
				"name" => "Sauder",
				"slug" => "sauder",
			],
		];

		foreach ($brands as $brand) {
			$newBrand = new Brand();
			$newBrand->name = $brand['name'];
			$newBrand->slug = $brand['slug'];
			$newBrand->save();
		}
	}
}
