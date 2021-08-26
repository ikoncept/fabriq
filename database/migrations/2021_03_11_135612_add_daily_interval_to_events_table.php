<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDailyIntervalToEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->integer('yearly_interval')->after('full_day')->nullable();
            $table->integer('monthly_interval')->after('full_day')->nullable();
            $table->integer('weekly_interval')->after('full_day')->nullable();
            $table->integer('daily_interval')->after('full_day')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['yearly_interval', 'monthly_interval', 'weekly_interval', 'daily_interval']);
        });
    }
}
