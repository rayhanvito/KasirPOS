<?php

namespace Database\Seeders;

use App\Classes\Common;
use App\Models\Category;
use App\Models\FrontWebsiteSettings;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FrontWebsiteSettingsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('front_website_settings')->delete();

        DB::statement('ALTER TABLE front_website_settings AUTO_INCREMENT = 1');

        $featuredCategories = [];
        $featuredCategories['electronifly'] = [
            'headphones',
            'soundbars',
            'mobiles',
            'televisions',
            'laptops',
            'desktops',
            'monitors',
            'clothes',
            'shoes',
        ];

        $featuredProducts = [];
        $featuredProducts['electronifly'] = [
            'munchkin-silicone-placemats',
            'zinus-sofa',
            'pampers-pants-girls-and-boy',
            'munchkin-duckling-bath-rinser',
            'apple-imac',
            'apple-iphone-13-pro-max',
        ];

        $linksWidget = [];
        $linksWidget['electronifly'] = [
            [
                'title' => 'Buy Now',
                'value' => 'https://1.envato.market/5bBAWb',
            ],
            [
                'title' => 'View Docs',
                'value' => 'https://docs.stockifly.in',
            ],
            [
                'title' => 'Live Preview',
                'value' => 'https://demo.stockifly.in',
            ]
        ];

        $pagesWidget = [];
        $pagesWidget['electronifly'] = [
            [
                'title' => 'Electronifly',
                'value' => 'http://demo.stockifly.in/store/electronifly',
            ],
            [
                'title' => 'Grocerifly',
                'value' => 'http://demo.stockifly.in/store/grocerifly',
            ],
            [
                'title' => 'Medifly',
                'value' => 'http://demo.stockifly.in/store/medifly',
            ],
        ];

        $description = [];
        $description['electronifly'] = "Electronifly have wide rage of elctornic items, We proivde you all electronic items with great discount and affordable rates. You can Shop Online for Men & Women Clothing, Shoes, Kid's fashion, Home Decor & Kitchen Appliances & Much More! Get Free Shipping with COD in India";

        $copyrightText = [];
        $copyrightText['electronifly'] = 'Copyright 2021 @ Electronifly, All rights reserved.';

        $faker = \Faker\Factory::create();

        $allWarehouses = Warehouse::select('id', 'name', 'slug')->get();
        foreach ($allWarehouses as $allWarehouse) {
            $bannerImages = [$allWarehouse->slug . '_banner_1.png', $allWarehouse->slug . '_banner_2.png', $allWarehouse->slug . '_banner_3.png'];

            $categories = Category::whereIn('slug', $featuredCategories[$allWarehouse->slug])->pluck('id')->toArray();
            $products = Product::whereIn('slug', $featuredProducts[$allWarehouse->slug])->pluck('id')->toArray();

            $contactWidget = [
                [
                    'title' => 'Email',
                    'value' => $faker->unique()->safeEmail,
                ],
                [
                    'title' => 'Phone',
                    'value' => $faker->e164PhoneNumber(),
                ],
                [
                    'title' => 'Address',
                    'value' => $faker->address(),
                ],
            ];

            $frontSetting = new FrontWebsiteSettings();
            $frontSetting->warehouse_id = $allWarehouse->id;
            $frontSetting->featured_categories = $categories;
            $frontSetting->featured_products = $products;
            $frontSetting->features_lists = [];
            $frontSetting->pages_widget = $pagesWidget[$allWarehouse->slug];
            $frontSetting->contact_info_widget = $contactWidget;
            $frontSetting->links_widget = $linksWidget[$allWarehouse->slug];
            $frontSetting->bottom_banners_1 = [$bannerImages[0]];
            $frontSetting->bottom_banners_2 = [$bannerImages[1]];
            $frontSetting->bottom_banners_3 = [$bannerImages[2]];
            $frontSetting->top_banners = [];
            $frontSetting->footer_company_description = $description[$allWarehouse->slug];
            $frontSetting->footer_copyright_text = $copyrightText[$allWarehouse->slug];
            $frontSetting->footer_company_description = $allWarehouse->name . " have many propular products wiht high discount and special offers.";
            $frontSetting->footer_copyright_text =  "Copyright " . date("Y") . " @ " . $allWarehouse->name . ", All rights reserved.";
            $frontSetting->save();

            foreach ($bannerImages as $bannerImage) {
                if (config('filesystems.default') == 'local') {
                    $folderPath = Common::getFolderPath('frontBannerPath');
                    $fileExists = Common::checkFileExists('frontBannerPath', $bannerImage);

                    if (!$fileExists) {
                        $url = 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/' . $bannerImage;
                        $contents = file_get_contents($url);
                        Storage::put($folderPath . '/' . $bannerImage, $contents);
                    }
                }
            }
        }
    }
}
