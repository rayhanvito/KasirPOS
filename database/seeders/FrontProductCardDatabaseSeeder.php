<?php

namespace Database\Seeders;

use App\Models\FrontProductCard;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FrontProductCardDatabaseSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		DB::table('front_product_cards')->delete();

		DB::statement('ALTER TABLE front_product_cards AUTO_INCREMENT = 1');

		$cards = [];
		$cards['electronifly'] = [
			[
				'title' => 'Limited Period Offer on mobiles',
				'subtitle' => "Big discount hurry up",
				'products' => [
					'samsung-galaxy-m32-5g',
					'apple-iphone-11-64gb-black',
					'oppo-a15s',
					'apple-iphone-12-128gb-green',
					'oppo-a74-5g',
					'apple-iphone-13-pro-max',
				],
			],
			[
				'title' => 'Decorate your home or office from best furnitures',
				'subtitle' => "Top picks for your home",
				'products' => [
					'zinus-metal-box-spring-mattress',
					'furinno-frame-computer-desk',
					'zinus-sofa',
					'sauder-costa-lateral-file',
					'furinno-office-computer-desk',
					'sauder-barrister-lane-executive-desk',
				],
			],
			[
				'title' => 'Shop according to your fashion',
				'subtitle' => "Grab best deals up to 50% off",
				'products' => [
					'lee-mens-t-shirt',
					'champion-mens-pant',
					'gildan-mens-sweatshirt',
					'lee-womens-jean',
					'lee-cooper-mens-sneakers',
					'puma-child-running-shoe',
				],
			],
			[
				'title' => 'Best gadgets according to top requirements',
				'subtitle' => "Upto 60% off",
				'products' => [
					'apple-airpods-max',
					'sony-noise-cancelling-headphones',
					'zebronics-bluetooth-soundbar',
					'asus-vivobook-15',
					'lenovo-ideapad-4',
					'dell-n3511-laptop',
					'apple-imac',
					'acer-aspire-desktop',
					'samsung-monitor',
					'lenovo-ideapad-slim-3',
					'sony-home-theatre-system',
					'asus-zen-aio-laptop',
				],
			],
			[
				'title' => 'Best Televisions Under Your Budget',
				'subtitle' => "Get world's best tv at affordable price",
				'products' => [
					'sony-75-inch-tv',
					'sony-bravia-google-tv',
					'samsung-crystal-4k-led-tv',
					'sony-bravia-4k-ultra-hd-tv',
					'samsung-smart-tv-with-alexa'
				],
			]
		];

		$allWarehouses = Warehouse::select('id', 'name', 'slug')->get();

		foreach ($allWarehouses as $allWarehouse) {
			$allCards = $cards[$allWarehouse->slug];

			if ($allCards) {
				foreach ($allCards as $allCard) {
					$allProducts = Product::whereIn('slug', $allCard['products'])->pluck('id')->toArray();

					$newProductCard = new FrontProductCard();
					$newProductCard->warehouse_id = $allWarehouse->id;
					$newProductCard->title = $allCard['title'];
					$newProductCard->subtitle = $allCard['subtitle'];
					$newProductCard->products = $allProducts;
					$newProductCard->save();
				}
			}
		}
	}
}
