<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CreateFrontWebsiteSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('front_website_settings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('warehouse_id')->unsigned()->nullable()->default(null);
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade')->onUpdate('cascade');
            $table->text('featured_categories');
            $table->string('featured_categories_title')->nullable()->default('Featured Categories');
            $table->string('featured_categories_subtitle')->nullable()->default('');
            $table->text('featured_products');
            $table->string('featured_products_title')->nullable()->default('Featured Products');
            $table->string('featured_products_subtitle')->nullable()->default('');

            $table->text('features_lists');

            //Social
            $table->string('facebook_url')->nullable()->default('');
            $table->string('twitter_url')->nullable()->default('');
            $table->string('instagram_url')->nullable()->default('');
            $table->string('linkedin_url')->nullable()->default('');
            $table->string('youtube_url')->nullable()->default('');

            $table->text('pages_widget');
            $table->text('contact_info_widget');
            $table->text('links_widget');

            $table->string('footer_company_description', 1000)->default("Stockify have many propular products wiht high discount and special offers.");
            $table->string('footer_copyright_text', 1000)->default("Copyright 2021 @ Stockify, All rights reserved.");

            $table->text('top_banners');
            $table->text('bottom_banners_1');
            $table->text('bottom_banners_2');
            $table->text('bottom_banners_3');

            $table->timestamps();
        });

        // Adding slug column in warehouses table
        Schema::table('warehouses', function (Blueprint $table) {
            $table->string('slug')->nullable()->default(null)->after('name');
            $table->string('dark_logo')->nullable()->default(NULL)->after('logo');
        });

        if (app_type() == 'non-saas') {
            $allWarehouses = DB::table('warehouses')->get();;
            foreach ($allWarehouses as $allWarehouse) {

                // Adding slug to warehouse for online store
                DB::table('warehouses')->where('id', $allWarehouse->id)
                    ->update([
                        'slug' => Str::slug($allWarehouse->name, '-')
                    ]);

                DB::table('front_website_settings')->insert([
                    'warehouse_id' => $allWarehouse->id,
                    'featured_categories' => json_encode([]),
                    'featured_products' => json_encode([]),
                    'features_lists' => json_encode([]),
                    'pages_widget' => json_encode([]),
                    'contact_info_widget' => json_encode([]),
                    'links_widget' => json_encode([]),
                    'top_banners' => json_encode([]),
                    'bottom_banners_1' => json_encode([]),
                    'bottom_banners_2' => json_encode([]),
                    'bottom_banners_3' => json_encode([]),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('front_website_settings');

        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->dropColumn('dark_logo');
        });
    }
}
