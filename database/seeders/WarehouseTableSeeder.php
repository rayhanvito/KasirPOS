<?php

namespace Database\Seeders;

use App\Classes\Common;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class WarehouseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Model::unguard();

        DB::table('warehouses')->delete();

        DB::statement('ALTER TABLE warehouses AUTO_INCREMENT = 1');

        $faker = \Faker\Factory::create();

        $warehouses = [
            [
                'name' => 'Electronifly',
                'slug' => 'electronifly',
                'email' => 'electronifly@example.com',
                "image" => 'electronifly.png',
                "image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/electronifly.png',
                "dark_image" => 'electronifly_dark.png',
                "dark_image_url" => 'https://stockifly-data.s3.ap-south-1.amazonaws.com/images/electronifly_dark.png',
            ]
        ];

        foreach ($warehouses as $warehouse) {
            $newWarehouse = new Warehouse();
            $newWarehouse->name = trim($warehouse['name']);
            $newWarehouse->slug = trim($warehouse['slug']);
            $newWarehouse->logo = trim($warehouse['image']);
            $newWarehouse->dark_logo = trim($warehouse['dark_image']);
            $newWarehouse->email = trim($warehouse['email']);
            $newWarehouse->phone = $faker->e164PhoneNumber();
            $newWarehouse->terms_condition = "1. Goods once sold will not be taken back or exchanged
	2. All disputes are subject to [ENTER_YOUR_CITY_NAME] jurisdiction only";
            $newWarehouse->save();

            if (config('filesystems.default') == 'local') {
                $folderPath = Common::getFolderPath('warehouseLogoPath');
                $fileExists = Common::checkFileExists('warehouseLogoPath', $warehouse['image']);
                $file1Exists = Common::checkFileExists('warehouseLogoPath', $warehouse['dark_image']);

                if (!$fileExists) {
                    $contents = file_get_contents($warehouse['image_url']);
                    Storage::put($folderPath . '/' . $warehouse['image'], $contents);
                }

                if (!$file1Exists) {
                    $contents = file_get_contents($warehouse['dark_image_url']);
                    Storage::put($folderPath . '/' . $warehouse['dark_image'], $contents);
                }
            }
        }
    }
}
