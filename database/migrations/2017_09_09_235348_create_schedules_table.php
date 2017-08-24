<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->integer('type_id')->unsigned();
            $table->foreign('type_id')->references('id')->on('types');
            $table->integer('channel_id')->unsigned();
            $table->foreign('channel_id')->references('id')->on('channels');

            $table->boolean('sunday_enabled')->default(true);
            $table->time('sunday_start')->default('000000');
            $table->time('sunday_end')->default('235959');
            $table->integer('sunday_lead_days')->default(2);

            $table->boolean('monday_enabled')->default(true);
            $table->time('monday_start')->default('000000');
            $table->time('monday_end')->default('235959');
            $table->integer('monday_lead_days')->default(2);

            $table->boolean('tuesday_enabled')->default(true);
            $table->time('tuesday_start')->default('000000');
            $table->time('tuesday_end')->default('235959');
            $table->integer('tuesday_lead_days')->default(2);

            $table->boolean('wednesday_enabled')->default(true);
            $table->time('wednesday_start')->default('000000');
            $table->time('wednesday_end')->default('235959');
            $table->integer('wednesday_lead_days')->default(2);

            $table->boolean('thursday_enabled')->default(true);
            $table->time('thursday_start')->default('000000');
            $table->time('thursday_end')->default('235959');
            $table->integer('thursday_lead_days')->default(2);

            $table->boolean('friday_enabled')->default(true);
            $table->time('friday_start')->default('000000');
            $table->time('friday_end')->default('235959');
            $table->integer('friday_lead_days')->default(2);

            $table->boolean('saturday_enabled')->default(true);
            $table->time('saturday_start')->default('000000');
            $table->time('saturday_end')->default('235959');
            $table->integer('saturday_lead_days')->default(2);

            $table->timestamps();
        });

        DB::table('schedules')->insert(
            array(
                array(
                    'company_id' => 1,
                    'type_id' => 2,
                    'channel_id' => 2
                ),
                array(
                    'company_id' => 1,
                    'type_id' => 2,
                    'channel_id' => 3
                ),
                array(
                    'company_id' => 1,
                    'type_id' => 2,
                    'channel_id' => 4
                )
            )
        );

        Schema::table('sms', function (Blueprint $table) {
            $table->integer('schedule_id')->unsigned()->after('message_id');
            $table->foreign('schedule_id')->references('id')->on('schedules');
        });

        Schema::table('emails', function (Blueprint $table) {
            $table->integer('schedule_id')->unsigned()->after('message_id');
            $table->foreign('schedule_id')->references('id')->on('schedules');
        });

        Schema::table('voices', function (Blueprint $table) {
            $table->integer('schedule_id')->unsigned()->after('message_id');
            $table->foreign('schedule_id')->references('id')->on('schedules');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('voices', function($table) {
            $table->dropForeign('voices_schedule_id_foreign');
            $table->dropColumn('schedule_id');
        });

        Schema::table('emails', function($table) {
            $table->dropForeign('emails_schedule_id_foreign');
            $table->dropColumn('schedule_id');
        });

        Schema::table('sms', function($table) {
            $table->dropForeign('sms_schedule_id_foreign');
            $table->dropColumn('schedule_id');
        });

        Schema::drop('schedules');
    }
}
