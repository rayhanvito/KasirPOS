<?php

namespace Database\Seeders;

use App\Classes\Common;
use App\Classes\LangTrans;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $isLogin = auth('api')->check() ? 'yes' : 'no';
        // $this->command->info($isLogin);
        if (env('APP_ENV') != 'envato') {

            $this->call(UnitTableSeeder::class);
            $this->call(WarehouseTableSeeder::class);
            $this->call(CurrencyTableSeeder::class);
            $this->call(LangTableSeeder::class);
            $this->call(PaymentModesTableSeeder::class);
            $this->call(CompanyTableSeeder::class);
            $this->call(RolesTableSeeder::class);
            $this->call(UsersTableSeeder::class);

            Common::createAllCompaniesWalkInCustomer();

            $this->call(BrandsTableSeeder::class);
            $this->call(CategoryTableSeeder::class);
            $this->call(ProductTableSeeder::class);
            $this->call(StockAdjustmentTableSeeder::class);
            $this->call(OrdersTableSeeder::class);
            $this->call(PaymentsTableSeeder::class);
            $this->call(ExpenseTableSeeder::class);
            $this->call(ExpenseTableSeeder::class);

            $this->call(FrontWebsiteSettingsDatabaseSeeder::class);
            $this->call(FrontProductCardDatabaseSeeder::class);

            // Remove All Settings created from migrations
            Model::unguard();
            DB::table('settings')->delete();
            DB::statement('ALTER TABLE settings AUTO_INCREMENT = 1');

            // assigning all tables company_id fields with
            // First Company
            $company = Company::first();
            Common::assignCompanyForNonSaas($company);

            // Creating SuperAdmin
            if (app_type() == 'saas') {
                \App\SuperAdmin\Classes\SuperAdminCommon::createSuperAdmin(true);
            }
        }
    }
}
