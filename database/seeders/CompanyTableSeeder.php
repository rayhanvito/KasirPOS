<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Currency;
use App\Models\Lang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Model::unguard();

        DB::table('companies')->delete();

        DB::statement('ALTER TABLE companies AUTO_INCREMENT = 1');

        $faker = \Faker\Factory::create();

        $warehouse = Warehouse::where('email', 'electronifly@example.com')->first();
        $currency = Currency::where('code', "USD")->first();
        $enLang = Lang::where('key', 'en')->first();

        $setting = new Company();
        $setting->name = 'Stockifly';
        $setting->short_name = 'Stockifly';
        $setting->email = 'company@example.com';
        $setting->phone = $faker->e164PhoneNumber();
        $setting->address = '7 street, city, state, 762782';
        $setting->currency_id = $currency->id;
        $setting->warehouse_id = $warehouse->id;
        $setting->lang_id = $enLang->id;
        $setting->white_label_completed = 1;
        $setting->save();

        $warehouse->company_id = $setting->id;
        $warehouse->save();
    }
}
