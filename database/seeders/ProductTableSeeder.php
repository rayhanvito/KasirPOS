<?php

namespace Database\Seeders;

use App\Classes\Common;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CustomField;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */

	public function run()
	{
		Model::unguard();

		DB::table('custom_fields')->delete();
		DB::table('products')->delete();

		DB::statement('ALTER TABLE custom_fields AUTO_INCREMENT = 1');
		DB::statement('ALTER TABLE products AUTO_INCREMENT = 1');
		DB::statement('ALTER TABLE product_details AUTO_INCREMENT = 1');

		$faker = \Faker\Factory::create();

		$customField = new CustomField();
		$customField->name = "Expiry Date";
		$customField->type = "date";
		$customField->save();

		$products =  [
			[
				"name" => "Sony Noise Cancelling Headphones",
				"image" => '61njwOtQSML._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/61njwOtQSML._AC_UY218_.jpg',
				"category"	=>	"headphones",
				"brand"	=>	"sony",
				"unit"	=>	"pc",
				"mrp"	=>	45,
				"purchase_price"	=>	35,
				"sales_price"	=>	41,
			],
			[
				"name" => "Apple EarPods",
				"image" => '51UEOS6bML._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/51UEOS6bML._AC_UY218_.jpg',
				"category"	=>	"headphones",
				"brand"	=>	"apple",
				"unit"	=>	"pc",
				"mrp"	=>	38,
				"purchase_price"	=>	31,
				"sales_price"	=>	34,
			],
			[
				"name" => "Sony Over-Ear Headphones",
				"image" => '715hXtbXdzL._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/715hXtbXdzL._AC_UY218_.jpg',
				"category"	=>	"headphones",
				"brand"	=>	"sony",
				"unit"	=>	"pc",
				"mrp"	=>	20,
				"purchase_price"	=>	15,
				"sales_price"	=>	11,
			],
			[
				"name" => "Apple AirPods Pro",
				"image" => '71bhWgQK-cL._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/71bhWgQK-cL._AC_UY218_.jpg',
				"category"	=>	"headphones",
				"brand"	=>	"apple",
				"unit"	=>	"pc",
				"mrp"	=>	25,
				"purchase_price"	=>	19,
				"sales_price"	=>	21,
			],
			[
				"name" => "Apple AirPods Max",
				"image" => '81thV7SoLZL._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/81thV7SoLZL._AC_UY218_.jpg',
				"category"	=>	"headphones",
				"brand"	=>	"apple",
				"unit"	=>	"pc",
				"mrp"	=>	29,
				"purchase_price"	=>	23,
				"sales_price"	=>	26,
			],
			[
				"name" => "Sony ZX110NC Headphones",
				"image" => '61njwOtQSML._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/61njwOtQSML._AC_UY218_.jpg',
				"category"	=>	"headphones",
				"brand"	=>	"sony",
				"unit"	=>	"pc",
				"mrp"	=>	18,
				"purchase_price"	=>	14,
				"sales_price"	=>	16,
			],
			[
				"name" => "Zebronics Bluetooth Soundbar",
				"image" => '813oEQBqFL._AC_UL604_SR604,400_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/813oEQBqFL._AC_UL604_SR604,400_.jpg',
				"category"	=>	"soundbars",
				"brand"	=>	"zebronics",
				"unit"	=>	"pc",
				"mrp"	=>	33,
				"purchase_price"	=>	29.98,
				"sales_price"	=>	31,
			],
			[
				"name" => "Sony Home Theatre System",
				"image" => '61zDrw-drKS._AC_UL604_SR604,400_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/61zDrw-drKS._AC_UL604_SR604,400_.jpg',
				"category"	=>	"soundbars",
				"brand"	=>	"sony",
				"unit"	=>	"pc",
				"mrp"	=>	45,
				"purchase_price"	=>	38,
				"sales_price"	=>	43,
			],
			[
				"name" => "Zebronics Soundbar with Dolby Atmos",
				"image" => '715yMrwmAbL._AC_UL604_SR604,400_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/715yMrwmAbL._AC_UL604_SR604,400_.jpg',
				"category"	=>	"soundbars",
				"brand"	=>	"zebronics",
				"unit"	=>	"pc",
				"mrp"	=>	43,
				"purchase_price"	=>	35,
				"sales_price"	=>	39,
			],
			[
				"name" => "Zebronics Multimedia soundbar",
				"image" => '81io-aPCPdL._AC_UL604_SR604,400_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/81io-aPCPdL._AC_UL604_SR604,400_.jpg',
				"category"	=>	"soundbars",
				"brand"	=>	"zebronics",
				"unit"	=>	"pc",
				"mrp"	=>	29,
				"purchase_price"	=>	27,
				"sales_price"	=>	28.15,
			],
			[
				"name" => "Lenovo IdeaPad Slim 3",
				"image" => '61Dw5Z8LzJL._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/61Dw5Z8LzJL._AC_UY218_.jpg',
				"category"	=>	"laptops",
				"brand"	=>	"lenova",
				"unit"	=>	"pc",
				"mrp"	=>	34,
				"purchase_price"	=>	26,
				"sales_price"	=>	29.15,
			],
			[
				"name" => "Lenovo ThinkPad 13 Ultrabook",
				"image" => '31oXtPh3GVL._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/31oXtPh3GVL._AC_UY218_.jpg',
				"category"	=>	"laptops",
				"brand"	=>	"lenova",
				"unit"	=>	"pc",
				"mrp"	=>	52,
				"purchase_price"	=>	46,
				"sales_price"	=>	49.15,
			],
			[

				"name" => "Lenovo IdeaPad 4",
				"image" => '61qNmdoJUbL._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/61qNmdoJUbL._AC_UY218_.jpg ',
				"category"	=>	"laptops",
				"brand"	=>	"lenova",
				"unit"	=>	"pc",
				"mrp"	=>	52,
				"purchase_price"	=>	46,
				"sales_price"	=>	49.15,
			],
			[
				"name" => "ASUS VivoBook 15",
				"image" => '71S8U9VzLTL._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/71S8U9VzLTL._AC_UY218_.jpg ',
				"category"	=>	"laptops",
				"brand"	=>	"asus",
				"unit"	=>	"pc",
				"mrp"	=>	42,
				"purchase_price"	=>	39,
				"sales_price"	=>	36.15,
			],
			[
				"name" => 'ASUS TUF Laptop',
				"image" => '914o5xV18L._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/914o5xV18L._AC_UY218_.jpg',
				"category"	=>	"laptops",
				"brand"	=>	"asus",
				"unit"	=>	"pc",
				"mrp"	=>	42,
				"purchase_price"	=>	39,
				"sales_price"	=>	36.15,
			],
			[
				"name" => 'Lenovo Light Laptop',
				"image" => '61q6x-ll5FL._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/61q6x-ll5FL._AC_UY218_.jpg',
				"category"	=>	"laptops",
				"brand"	=>	"lenova",
				"unit"	=>	"pc",
				"mrp"	=>	42,
				"purchase_price"	=>	39,
				"sales_price"	=>	36.15,
			],
			[
				"name" => "Dell N3511 Laptop",
				"image" => '61XR0oGhF6L._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/61XR0oGhF6L._AC_UY218_.jpg',
				"category"	=>	"laptops",
				"brand"	=>	"dell",
				"unit"	=>	"pc",
				"mrp"	=>	42,
				"purchase_price"	=>	39,
				"sales_price"	=>	36.15,
			],
			[
				"name" => "OPPO A74 5G",
				"image" => '71poFSdDs5S._SX679.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/71poFSdDs5S._SX679.jpg',
				"category"	=>	"mobiles",
				"brand"	=>	"oppo",
				"unit"	=>	"pc",
				"mrp"	=>	33,
				"purchase_price"	=>	28,
				"sales_price"	=>	31.15,
			],
			[
				"name" => 'OPPO A15s',
				"image" => '71TdXNLT1FL._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/71TdXNLT1FL._AC_UY218_.jpg',
				"category"	=>	"mobiles",
				"brand"	=>	"oppo",
				"unit"	=>	"pc",
				"mrp"	=>	33,
				"purchase_price"	=>	28,
				"sales_price"	=>	31.15,
			],
			[
				"name" => "Samsung Galaxy M32 5G",
				"image" => '71os5DRhuSL._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/71os5DRhuSL._AC_UY218_.jpg',
				"category"	=>	"mobiles",
				"brand"	=>	"samsung",
				"unit"	=>	"pc",
				"mrp"	=>	34,
				"purchase_price"	=>	29,
				"sales_price"	=>	32.25,
			],
			[
				"name" => "Samsung Galaxy M32 4G",
				"image" => '71QT7dSK4BL._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/71QT7dSK4BL._AC_UY218_.jpg',
				"category"	=>	"mobiles",
				"brand"	=>	"samsung",
				"unit"	=>	"pc",
				"mrp"	=>	34,
				"purchase_price"	=>	29,
				"sales_price"	=>	32.25,
			],
			[
				"name" => "Apple iPhone 13 Pro Max",
				"image" => '61i8Vjb17SL._AC_UL320_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/61i8Vjb17SL._AC_UL320_.jpg',
				"category"	=>	"mobiles",
				"brand"	=>	"apple",
				"unit"	=>	"pc",
				"mrp"	=>	43,
				"purchase_price"	=>	37,
				"sales_price"	=>	41,
			],
			[
				"name" => "Apple iPhone 12 (128GB) - Green",
				"image" => '71cQWYVtcBL._AC_UL320_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/71cQWYVtcBL._AC_UL320_.jpg',
				"category"	=>	"mobiles",
				"brand"	=>	"apple",
				"unit"	=>	"pc",
				"mrp"	=>	46,
				"purchase_price"	=>	40,
				"sales_price"	=>	43,
			],
			[
				"name" => "Apple iPhone 11 (64GB) - Black",
				"image" => '71i2XhHU3pL._AC_UL320_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/71i2XhHU3pL._AC_UL320_.jpg',
				"category"	=>	"mobiles",
				"brand"	=>	"apple",
				"unit"	=>	"pc",
				"mrp"	=>	44,
				"purchase_price"	=>	40,
				"sales_price"	=>	41,
			],
			[

				"name" => "Samsung Crystal 4K LED TV",
				"image" => '61GwJAhftvS._SX569_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/61GwJAhftvS._SX569_.jpg',
				"category"	=>	"televisions",
				"brand"	=>	"samsung",
				"unit"	=>	"pc",
				"mrp"	=>	56,
				"purchase_price"	=>	48,
				"sales_price"	=>	52,
			],
			[
				"name" => "Sony Bravia 4K Ultra HD TV",
				"image" => '91Pij5DqU1L._SX450_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/91Pij5DqU1L._SX450_.jpg',
				"category"	=>	"televisions",
				"brand"	=>	"sony",
				"unit"	=>	"pc",
				"mrp"	=>	612,
				"purchase_price"	=>	549,
				"sales_price"	=>	585,
			],
			[
				"name" => "Sony Bravia Google TV ",
				"image" => '91Pij5DqU1L._SX450_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/91Pij5DqU1L._SX450_.jpg',
				"category"	=>	"televisions",
				"brand"	=>	"sony",
				"unit"	=>	"pc",
				"mrp"	=>	589,
				"purchase_price"	=>	537,
				"sales_price"	=>	549,
			],
			[

				"name" => "Sony 75 Inch TV",
				"image" => '91RfzivKmwL._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/91RfzivKmwL._AC_UY218_.jpg',
				"category"	=>	"televisions",
				"brand"	=>	"sony",
				"unit"	=>	"pc",
				"mrp"	=>	2168,
				"purchase_price"	=>	2000,
				"sales_price"	=>	2075,
			],
			[
				"name" => "SAMSUNG Smart TV with Alexa ",
				"image" => '71LJJrKbezL._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/71LJJrKbezL._AC_UY218_.jpg',
				"category"	=>	"televisions",
				"brand"	=>	"samsung",
				"unit"	=>	"pc",
				"mrp"	=>	6835,
				"purchase_price"	=> 6523,
				"sales_price"	=>	6720,
			],
			[
				"name" => "Acer Aspire Desktop",
				"image" => '71QfqMtNISL._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/71QfqMtNISL._AC_UY218_.jpg',
				"category"	=>	"desktops",
				"brand"	=>	"acer",
				"unit"	=>	"pc",
				"mrp"	=>	621.13,
				"purchase_price"	=> 601,
				"sales_price"	=>	618,
			],
			[

				"name" => "Dell OptiPlex Desktop Computer",
				"image" => '61y0UKNkt9L._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/61y0UKNkt9L._AC_UY218_.jpg',
				"category"	=>	"desktops",
				"brand"	=>	"dell",
				"unit"	=>	"pc",
				"mrp"	=>	765.13,
				"purchase_price"	=> 727,
				"sales_price"	=>	758,
			],
			[
				"name" => "Apple iMac",
				"image" => '81blwMhVV8L._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/81blwMhVV8L._AC_UY218_.jpg',
				"category"	=>	"desktops",
				"brand"	=>	"dell",
				"unit"	=>	"pc",
				"mrp"	=>	1090,
				"purchase_price"	=> 999.26,
				"sales_price"	=>	978,
			],
			[

				"name" => "ASUS Zen AiO Laptop",
				"image" => '41R0UJizC2L._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/41R0UJizC2L._AC_UY218_.jpg',
				"category"	=>	"desktops",
				"brand"	=>	"asus",
				"unit"	=>	"pc",
				"mrp"	=>	869,
				"purchase_price"	=> 801,
				"sales_price"	=>	857,
			],
			[

				"name" => "Dell Gaming Monitor",
				"image" => '41R0UJizC2L._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/41R0UJizC2L._AC_UY218_.jpg',
				"category"	=>	"monitors",
				"brand"	=>	"dell",
				"unit"	=>	"pc",
				"mrp"	=>	426,
				"purchase_price"	=> 401,
				"sales_price"	=>	422,
			],
			[
				"name" => "SAMSUNG Monitor",
				"image" => '81XKPle6OAL._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/81XKPle6OAL._AC_UY218_.jpg',
				"category"	=>	"monitors",
				"brand"	=>	"samsung",
				"unit"	=>	"pc",
				"mrp"	=>	269,
				"purchase_price"	=> 189,
				"sales_price"	=>	203,
			],
			[
				"name" => "ASUS Eye Care Display Monitor",
				"image" => '81eGtZyyavL._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/81eGtZyyavL._AC_UY218_.jpg',
				"category"	=>	"monitors",
				"brand"	=>	"asus",
				"unit"	=>	"pc",
				"mrp"	=>	249,
				"purchase_price"	=> 198,
				"sales_price"	=>	234,
			],
			[
				"name" => 'ASUS Portable Monitor',
				"image" => '81ufQKVY2fL._AC_UY218_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/81ufQKVY2fL._AC_UY218_.jpg',
				"category"	=>	"monitors",
				"brand"	=>	"asus",
				"unit"	=>	"pc",
				"mrp"	=>	189,
				"purchase_price"	=> 172.99,
				"sales_price"	=>	177,
			],
			[
				"name" => "Gildan Men's Sweatshirt",
				"image" => '91hPxXNfwTS._AC_UL320_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/91hPxXNfwTS._AC_UL320_.jpg',
				"category"	=>	"clothes",
				"brand"	=>	"gildan",
				"unit"	=>	"pc",
				"mrp"	=>	27,
				"purchase_price"	=> 18.99,
				"sales_price"	=>	21,
			],
			[
				"name" => "Lee Women's Jean",
				"image" => '917A-DUZbgL._AC_UL320_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/917A-DUZbgL._AC_UL320_.jpg',
				"category"	=>	"clothes",
				"brand"	=>	"lee",
				"unit"	=>	"pc",
				"mrp"	=>	59,
				"purchase_price"	=> 50.99,
				"sales_price"	=>	53,
			],
			[
				"name" => "Champion Men's Pant",
				"image" => '71yVf4x3DIL._AC_UL320_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/71yVf4x3DIL._AC_UL320_.jpg',
				"category"	=>	"clothes",
				"brand"	=>	"champion",
				"unit"	=>	"pc",
				"mrp"	=>	15.19,
				"purchase_price"	=> 12.99,
				"sales_price"	=>	14,
			],
			[

				"name" => "LEE Men's Regular Fit Jean",
				"image" => '91B3eSkmUUL._AC_UL320_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/91B3eSkmUUL._AC_UL320_.jpg',
				"category"	=>	"clothes",
				"brand"	=>	"lee",
				"unit"	=>	"pc",
				"mrp"	=>	44,
				"purchase_price"	=> 38.99,
				"sales_price"	=>	41,
			],
			[
				"name" => "Lee Men's Slim Straight Leg Jeans",
				"image" => '91yLNaTtAEL._AC_UL320_.jpg',
				"image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/91yLNaTtAEL._AC_UL320_.jpg',
				"category"	=>	"clothes",
				"brand"	=>	"lee",
				"unit"	=>	"pc",
				"mrp"	=>	43,
				"purchase_price"	=> 37.99,
				"sales_price"	=>	41,
			],
			[
				"name" => "Lee Polo Shirt",
				"image" => '91yLNaTtAEL._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/91yLNaTtAEL._AC_UL320_.jpg",
				"category"	=>	"clothes",
				"brand"	=>	"lee",
				"unit"	=>	"pc",
				"mrp"	=>	23,
				"purchase_price"	=> 18,
				"sales_price"	=>	21,
			],
			[
				"name" => "Lee Men's T-Shirt",
				"image" => '81PdFU9yIlL._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/81PdFU9yIlL._AC_UL320_.jpg",
				"category"	=>	"clothes",
				"brand"	=>	"lee",
				"unit"	=>	"pc",
				"mrp"	=>	23,
				"purchase_price"	=> 18,
				"sales_price"	=>	21,
			],
			[
				"name" => "adidas Tennis Shoes",
				"image" => '61FbqzPD8TL._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/61FbqzPD8TL._AC_UL320_.jpg",
				"category"	=>	"shoes",
				"brand"	=>	"adidas",
				"unit"	=>	"pc",
				"mrp"	=>	65,
				"purchase_price"	=> 48,
				"sales_price"	=>	56,
			],
			[
				"name" => "adidas Golf Shoes",
				"image" => '81XU2Iy9ZFL._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/81XU2Iy9ZFL._AC_UL320_.jpg",
				"category"	=>	"shoes",
				"brand"	=>	"adidas",
				"unit"	=>	"pc",
				"mrp"	=>	65,
				"purchase_price"	=> 44,
				"sales_price"	=>	56,
			],
			[
				"name" => "PUMA Child Running Shoe",
				"image" => '71F5chCXQqS._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/71F5chCXQqS._AC_UL320_.jpg",
				"category"	=>	"shoes",
				"brand"	=>	"puma",
				"unit"	=>	"pc",
				"mrp"	=>	52,
				"purchase_price"	=> 44,
				"sales_price"	=>	49,
			],
			[
				"name" => "PUMA Running Shoe",
				"image" => '71KIA9w8ZiL._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/71KIA9w8ZiL._AC_UL320_.jpg",
				"category"	=>	"shoes",
				"brand"	=>	"puma",
				"unit"	=>	"pc",
				"mrp"	=>	71,
				"purchase_price"	=> 62,
				"sales_price"	=>	67,
			],
			[
				"name" => "Reebok Men's Workout Plus Sneaker",
				"image" => '515ceC2U1YL._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/515ceC2U1YL._AC_UL320_.jpg",
				"category"	=>	"shoes",
				"brand"	=>	"reebok",
				"unit"	=>	"pc",
				"mrp"	=>	71,
				"purchase_price"	=> 62,
				"sales_price"	=>	67,
			],
			[
				"name" => "Reebok Walking Shoe",
				"image" => '61K2Lm0RnxL._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/61K2Lm0RnxL._AC_UL320_.jpg",
				"category"	=>	"shoes",
				"brand"	=>	"reebok",
				"unit"	=>	"pc",
				"mrp"	=>	89,
				"purchase_price"	=> 73,
				"sales_price"	=>	82,
			],
			[
				"name" => "Lee Cooper Mens Sneakers",
				"image" => '816jUdRltL._UX625_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/816jUdRltL._UX625_.jpg",
				"category"	=>	"shoes",
				"brand"	=>	"lee-cooper",
				"unit"	=>	"pc",
				"mrp"	=>	89,
				"purchase_price"	=> 73,
				"sales_price"	=>	82,
			],
			[

				"name" => "Glad ForceFlex Bags",
				"image" => '81uXwVIixJL._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/81uXwVIixJL._AC_UL320_.jpg",
				"category"	=>	"grocery",
				"brand"	=>	"glad",
				"unit"	=>	"pc",
				"mrp"	=>	11,
				"purchase_price"	=> 7,
				"sales_price"	=>	9,
			],
			[
				"name" => "Maldon Salt, Sea Salt Flakes",
				"image" => '6115wVoviML._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/6115wVoviML._AC_UL320_.jpg",
				"category"	=>	"grocery",
				"brand"	=>	"maldon",
				"unit"	=>	"pc",
				"mrp"	=>	22,
				"purchase_price"	=> 14,
				"sales_price"	=>	17.12,
			],
			[
				"name" => "Welch's Fruit Snacks, Mixed Fruit, Gluten Free",
				"image" => '91F9-E5FL._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/91F9-E5FL._AC_UL320_.jpg",
				"category"	=>	"grocery",
				"brand"	=>	"welch's",
				"unit"	=>	"pc",
				"mrp"	=>	19,
				"purchase_price"	=> 14,
				"sales_price"	=>	17.12,
			],
			[
				"name" => "Peet's Coffee",
				"image" => '81VnM8QOKTL._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/81VnM8QOKTL._AC_UL320_.jpg",
				"category"	=>	"grocery",
				"brand"	=>	"peet's",
				"unit"	=>	"pc",
				"mrp"	=>	47,
				"purchase_price"	=> 34,
				"sales_price"	=>	41.12,
			],
			[
				"name" => "Tostitos Rounds Salsa Cups Nacho",
				"image" => '81XRi95WamL._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/81XRi95WamL._AC_UL320_.jpg",
				"category"	=>	"grocery",
				"brand"	=>	"tostitos",
				"unit"	=>	"pc",
				"mrp"	=>	27,
				"purchase_price"	=> 19,
				"sales_price"	=>	22,
			],
			[
				"name" => "Grandma's Cookies",
				"image" => '91afkHiUzDS._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/91afkHiUzDS._AC_UL320_.jpg",
				"category"	=>	"grocery",
				"brand"	=>	"grandma's ",
				"unit"	=>	"pc",
				"mrp"	=>	27,
				"purchase_price"	=> 19,
				"sales_price"	=>	22,
			],
			[
				"name" => "Graco Chair",
				"image" => '71DX30b0OML._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/71DX30b0OML._AC_UL320_.jpg",
				"category"	=>	"baby-kids",
				"brand"	=>	"graco",
				"unit"	=>	"pc",
				"mrp"	=>	147,
				"purchase_price"	=> 129,
				"sales_price"	=>	138,
			],
			[
				"name" => "Graco Car Seat",
				"image" => '71cTvloW3-L._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/71cTvloW3-L._AC_UL320_.jpg",
				"category"	=>	"baby-kids",
				"brand"	=>	"graco",
				"unit"	=>	"pc",
				"mrp"	=>	187,
				"purchase_price"	=> 176,
				"sales_price"	=>	182,
			],
			[
				"name" => "Munchkin Silicone Placemats",
				"image" => '71Wb4KtcoEL._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/71Wb4KtcoEL._AC_UL320_.jpg",
				"category"	=>	"baby-kids",
				"brand"	=>	"munchkin",
				"unit"	=>	"pc",
				"mrp"	=>	12,
				"purchase_price"	=> 9,
				"sales_price"	=>	10.16,
			],
			[
				"name" => "Munchkin Duckling Bath Rinser",
				"image" => '61ngmIT6ofL._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/61ngmIT6ofL._AC_UL320_.jpg",
				"category"	=>	"baby-kids",
				"brand"	=>	"munchkin",
				"unit"	=>	"pc",
				"mrp"	=>	12,
				"purchase_price"	=> 9,
				"sales_price"	=>	10,
			],
			[
				"name" => "Pampers Pants Girls and Boy",
				"image" => '81eLbe2HAiL._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/81eLbe2HAiL._AC_UL320_.jpg",
				"category"	=>	"baby-kids",
				"brand"	=>	"pampers",
				"unit"	=>	"pc",
				"mrp"	=>	22,
				"purchase_price"	=> 13,
				"sales_price"	=>	18,
			],
			[
				"name" => "Infantino Flip Carrier",
				"image" => '881S-xWVH8kL._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/81S-xWVH8kL._AC_UL320_.jpg",
				"category"	=>	"baby-kids",
				"brand"	=>	"infantino",
				"unit"	=>	"pc",
				"mrp"	=>	39,
				"purchase_price"	=> 33,
				"sales_price"	=>	35,
			],
			[
				"name" => "ZINUS Metal Box Spring Mattress",
				"image" => '81QWTlMeqfL._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/81QWTlMeqfL._AC_UL320_.jpg",
				"category"	=>	"home-and-furnitures",
				"brand"	=>	"zinus",
				"unit"	=>	"pc",
				"mrp"	=>	239,
				"purchase_price"	=> 213,
				"sales_price"	=>	235,
			],
			[
				"name" => "ZINUS Sofa",
				"image" => '91N7yBUrirL._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/91N7yBUrirL._AC_UL320_.jpg",
				"category"	=>	"home-and-furnitures",
				"brand"	=>	"zinus",
				"unit"	=>	"pc",
				"mrp"	=>	279,
				"purchase_price"	=> 253,
				"sales_price"	=>	265,
			],
			[

				"name" => "Furinno Office Computer Desk",
				"image" => '71MUVgvmJIL._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/71MUVgvmJIL._AC_UL320_.jpg",
				"category"	=>	"home-and-furnitures",
				"brand"	=>	"furinno",
				"unit"	=>	"pc",
				"mrp"	=>	79,
				"purchase_price"	=> 53,
				"sales_price"	=>	65,
			],
			[
				"name" => "Furinno Frame Computer Desk",
				"image" => '71mJ-owgDlL._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/71mJ-owgDlL._AC_UL320_.jpg",
				"category"	=>	"home-and-furnitures",
				"brand"	=>	"furinno",
				"unit"	=>	"pc",
				"mrp"	=>	49,
				"purchase_price"	=> 43,
				"sales_price"	=>	45,
			],
			[
				"name" => "Sauder Costa Lateral File",
				"image" => '81QV3GYz4zL._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/81QV3GYz4zL._AC_UL320_.jpg",
				"category"	=>	"home-and-furnitures",
				"brand"	=>	"sauder",
				"unit"	=>	"pc",
				"mrp"	=>	49,
				"purchase_price"	=> 43,
				"sales_price"	=>	45,
			],
			[
				"name" => "Sauder Barrister Lane Executive Desk",
				"image" => '81sqJgoTVmL._AC_UL320_.jpg',
				"image_url" => "https://stockifly-data.s3.ap-south-1.amazonaws.com/images/81sqJgoTVmL._AC_UL320_.jpg",
				"category"	=>	"home-and-furnitures",
				"brand"	=>	"sauder",
				"unit"	=>	"pc",
				"mrp"	=>	39,
				"purchase_price"	=> 33,
				"sales_price"	=>	37,
			]
		];

		$warehouses = Warehouse::select('id')->get();

		foreach ($products as $product) {
			$category = Category::where('slug', $product['category'])->first();
			$brand = Brand::where('slug', $product['brand'])->first();
			$unit = Unit::where('short_name', $product['unit'])->first();
			$randomUser = User::select('id')->inRandomOrder()->where('status', 'enabled')->first();
			$itemCode = $faker->randomNumber(5, true) . '' . $faker->randomNumber(5, true);

			$newProduct = new Product();
			$newProduct->name = trim($product['name']);
			$newProduct->image = trim($product['image']);
			$newProduct->slug = Str::slug(trim($product['name']), '-');
			$newProduct->barcode_symbology = "CODE128";
			$newProduct->item_code = (int) $itemCode;
			$newProduct->category_id = $category->id;
			$newProduct->brand_id = $brand->id;
			$newProduct->unit_id = $unit->id;
			$newProduct->user_id = $randomUser->id;
			$newProduct->save();

			if (config('filesystems.default') == 'local') {
				$folderPath = Common::getFolderPath('productImagePath');
				$fileExists = Common::checkFileExists('productImagePath', $product['image']);

				if (!$fileExists) {
					$contents = file_get_contents($product['image_url']);
					Storage::put($folderPath . '/' . $product['image'], $contents);
				}
			}

			foreach ($warehouses as $warehouse) {
				$newProductDetails = new ProductDetails();
				$newProductDetails->warehouse_id = $warehouse->id;
				$newProductDetails->product_id = $newProduct->id;
				$newProductDetails->mrp = $product['mrp'];
				$newProductDetails->purchase_price = $product['purchase_price'];
				$newProductDetails->sales_price = $product['sales_price'];
				$newProductDetails->stock_quantitiy_alert = $faker->randomElement([10, 20, 25, 30, 35, 40, 45, 50, 55, 70]);
				$newProductDetails->opening_stock = $faker->randomNumber(2, false);
				$newProductDetails->save();

				Common::recalculateOrderStock($newProductDetails->warehouse_id, $newProduct->id);
			}
		}
	}
}
