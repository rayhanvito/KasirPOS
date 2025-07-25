<?php

use App\Classes\NotificationSeed;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_type');
            $table->string('name');
            $table->string('name_key');
            $table->text('credentials')->nullable()->default(null);
            $table->text('other_data')->nullable()->default(null);
            $table->boolean('status')->default(false);
            $table->boolean('verified')->default(false);
            $table->timestamps();
        });

        if (app_type() == 'non-saas') {
            DB::table('settings')->insert([
                [
                    'setting_type' => 'storage',
                    'name' => 'Local',
                    'name_key' => 'local',
                    'credentials' => json_encode([]),
                    'status' => 1,
                ],
                [
                    'setting_type' => 'storage',
                    'name' => 'AWS',
                    'name_key' => 'aws',
                    'credentials' => json_encode([
                        'driver' => 's3',
                        'key' => '',
                        'secret' => '',
                        'region' => '',
                        'bucket' => '',
                    ]),
                    'status' => 0,
                ],
                [
                    'setting_type' => 'email',
                    'name' => 'SMTP',
                    'name_key' => 'smtp',
                    'credentials' => json_encode([
                        'from_name' => '',
                        'from_email' => '',
                        'host' => '',
                        'port' => '',
                        'encryption' => '',
                        'username' => '',
                        'password' => '',
                    ]),
                    'status' => 0,
                ],
                [
                    'setting_type' => 'send_mail_settings',
                    'name' => 'Send mail to warehouse',
                    'name_key' => 'warehouse',
                    'credentials' => json_encode([]),
                    'status' => 0,
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
