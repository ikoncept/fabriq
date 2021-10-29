<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCropColumnsToImagesAndVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->string('y_position', 4)->default('50%')->after('caption');
            $table->string('x_position', 4)->default('50%')->after('caption');
            $table->boolean('custom_crop')->default(0)->after('caption');
        });
        Schema::table('videos', function (Blueprint $table) {
            $table->string('y_position', 4)->default('50%')->after('caption');
            $table->string('x_position', 4)->default('50%')->after('caption');
            $table->boolean('custom_crop')->default(0)->after('caption');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(config('app.env') !== 'testing') {
            Schema::table('images', function (Blueprint $table) {
                $table->dropColumn('custom_crop');
                $table->dropColumn('y_position');
                $table->dropColumn('x_position');
            });
            Schema::table('videos', function (Blueprint $table) {
                $table->dropColumn('custom_crop');
                $table->dropColumn('y_position');
                $table->dropColumn('x_position');
            });
        }
    }
}
