<?php

use App\SuperAdmin\Models\GlobalCompany;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWhiteLabelCompleteColumnInCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('white_label_completed')->default(false);
        });

        $globalCompany = GlobalCompany::first();
        $globalCompany->white_label_completed = env('APP_ENV') == 'production' ? 1 : 0;
        $globalCompany->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('white_label_completed');
        });
    }
}
