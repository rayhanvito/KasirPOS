<?php

namespace Database\Seeders;

use App\Classes\Common;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Model::unguard();

        DB::table('units')->delete();

        DB::statement('ALTER TABLE units AUTO_INCREMENT = 1');

        $allUnits = Common::allUnits();

        foreach ($allUnits as $allUnit) {
            DB::table('units')->insert([
                'name' => $allUnit['name'],
                'short_name' => $allUnit['short_name'],
                'operator' => $allUnit['operator'],
                'operator_value' => $allUnit['operator_value'],
                'is_deletable' => false,
            ]);
        }
    }
}
