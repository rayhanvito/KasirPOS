<?php

namespace Database\Seeders;

use App\Classes\Common;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoryTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */

	public function run()
	{
		Model::unguard();

		DB::table('categories')->delete();

		DB::statement('ALTER TABLE categories AUTO_INCREMENT = 1');

		$categories =  [
			[
				"name" => "Electronics",
				"slug" => "electronics",
				"parent_id" => null,
				"image" => 'category_1.png',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/category_1.png',
			],
			[
				"name" => "Audio",
				"slug" => "audio",
				"parent_id" => "electronics",
				"image" => 'category_2.png',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/category_2.png',
			],
			[
				"name" => "Headphones",
				"slug" => "headphones",
				"parent_id" => "audio",
				"image" => 'category_18.png',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/category_18.png',
			],
			[
				"name" => "Soundbars",
				"slug" => "soundbars",
				"parent_id" => "audio",
				"image" => 'category_3.png',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/category_3.png',
			],
			[
				"name" => "Mobiles",
				"slug" => "mobiles",
				"parent_id" => "electronics",
				"image" => 'category_4.png',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/category_4.png',
			],
			[
				"name" => "Televisions",
				"slug" => "televisions",
				"parent_id" => "electronics",
				"image" => 'category_5.png',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/category_5.png',
			],
			[
				"name" => "Computers",
				"slug" => "computers",
				"parent_id" => "electronics",
				"image" => 'category_6.png',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/category_6.png',
			],
			[
				"name" => "Laptops",
				"slug" => "laptops",
				"parent_id" => "computers",
				"image" => 'category_7.png',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/category_7.png',
			],
			[
				"name" => "Desktops",
				"slug" => "desktops",
				"parent_id" => "computers",
				"image" => 'category_8.png',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/category_8.png',
			],
			[
				"name" => "Computer Peripheral",
				"slug" => "computer-peripheral",
				"parent_id" => "computers",
				"image" => 'category_9.png',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/category_9.png',
			],
			[
				"name" => "Monitors",
				"slug" => "monitors",
				"parent_id" => "computer-peripheral",
				"image" => 'category_10.png',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/category_10.png',
			],
			[
				"name" => "Printers",
				"slug" => "printers",
				"parent_id" => "computer-peripheral",
				"image" => 'category_11.png',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/category_11.png',
			],




			[
				"name" => "Fashion",
				"slug" => "fashion",
				"parent_id" => null,
				"image" => 'category_12.png',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/category_12.png',
			],
			[
				"name" => "Clothes",
				"slug" => "clothes",
				"parent_id" => "fashion",
				"image" => 'category_13.png',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/category_13.png',
			],
			[
				"name" => "Shoes",
				"slug" => "shoes",
				"parent_id" => "fashion",
				"image" => 'category_14.png',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/category_14.png',
			],



			[
				"name" => "Grocery",
				"slug" => "grocery",
				"parent_id" => null,
				"image" => 'category_15.png',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/category_15.png',
			],
			[
				"name" => "Home and Furnitures",
				"slug" => "home-and-furnitures",
				"parent_id" => null,
				"image" => 'category_16.png',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/category_16.png',
			],
			[
				"name" => "Baby & Kids",
				"slug" => "baby-kids",
				"parent_id" => null,
				"image" => 'category_17.png',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/category_17.png',
			],
		];

		foreach ($categories as $category) {
			$parentId = null;

			if ($category['parent_id'] != null) {
				$parentCategory = Category::where('slug', $category['parent_id'])->first();

				$parentId = $parentCategory ? $parentCategory->id : null;
			}

			$newCategory = new Category();
			$newCategory->name = $category['name'];
			$newCategory->slug = $category['slug'];
			$newCategory->image = $category['slug'];
			$newCategory->image = $category['image'];
			$newCategory->parent_id = $parentId;
			$newCategory->save();

			if (config('filesystems.default') == 'local') {
				$folderPath = Common::getFolderPath('categoryImagePath');
				$fileExists = Common::checkFileExists('categoryImagePath', $category['image']);

				if (!$fileExists) {
					$contents = file_get_contents($category['image_url']);
					Storage::put($folderPath . '/' . $category['image'], $contents);
				}
			}
		}
	}
}
