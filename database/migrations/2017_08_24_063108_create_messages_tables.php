<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('display_name');
            $table->string('description');
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });

        DB::table('channels')->insert([
            array(
                'display_name' => 'message',
                'description' => ''
            ),
            array(
                'display_name' => 'sms',
                'description' => ''
            ),
            array(
                'display_name' => 'voice',
                'description' => ''
            ),
            array(
                'display_name' => 'email',
                'description' => ''
            )
            ]
        );

        Schema::create('statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('display_name');
            $table->string('description');
            $table->integer('channel_id')->unsigned();
            $table->foreign('channel_id')->references('id')->on('channels');
            $table->boolean('enabled')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('statuses')->insert([
            array(
                'display_name' => 'new',
                'description' => '',
                'channel_id' => 1
            ),
            array(
                'display_name' => 'scheduled',
                'description' => '',
                'channel_id' => 1
            ),
            array(
                'display_name' => 'confirmed',
                'description' => '',
                'channel_id' => 1
            ),
            array(
                'display_name' => 'cancelled',
                'description' => '',
                'channel_id' => 1
            ),
            array(
                'display_name' => 'rescheduled',
                'description' => '',
                'channel_id' => 1
            ),
            array(
                'display_name' => 'failed',
                'description' => '',
                'channel_id' => 1
            )]
        );

        Schema::create('types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('display_name')->default('appointment');
            $table->string('description')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('types')->insert([
            array(
                'display_name' => 'single',
                'description' => ''
            ),
            array(
                'display_name' => 'appointment',
                'description' => ''
            )]
        );

        Schema::create('contacts', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies');

            $table->string('phone_number');
            $table->string('email_address')->nullable();

            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid');

            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('type_id')->unsigned()->default(1);
            $table->foreign('type_id')->references('id')->on('types');

            $table->integer('status_id')->unsigned()->default(1);
            $table->foreign('status_id')->references('id')->on('statuses');

            $table->integer('contact_id')->unsigned();
            $table->foreign('contact_id')->references('id')->on('contacts');

            $table->string('parameters')->nullable();

            $table->boolean('fulfilled')->default(false);
            $table->integer('channel_id')->unsigned()->nullable();
            $table->foreign('channel_id')->references('id')->on('channels');

            $table->integer('priority')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('voice_scripts', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('type_id')->unsigned()->default(1);
            $table->foreign('type_id')->references('id')->on('types');

            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('voice_scripts')->insert(
            array(
                'company_id' => 1,
                'type_id' => 2
            )
        );

        Schema::create('voice_script_parts', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('voice_script_id')->unsigned();
            $table->foreign('voice_script_id')->references('id')->on('voice_scripts');

            $table->integer('sequence');
            $table->string('action');
            $table->string('input')->nullable();
            $table->string('options')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('voice_script_parts')->insert(
            array(
                array(
                    'voice_script_id' => 1,
                    'sequence' => 1,
                    'action' => 'say',
                    'input' => 'Hello {first_name}. This is a reminder of your upcoming appointment scheduled for {date_string} at {time} with {location}.'
                ),
                array(
                    'voice_script_id' => 1,
                    'sequence' => 2,
                    'action' => 'gather',
                    'options' => '{"action":"http:\/\/notiifii.ngrok.io\/voice\/gather","method":"GET","numDigits":1}'
                ),
                array(
                    'voice_script_id' => 1,
                    'sequence' => 3,
                    'action' => 'say',
                    'input' => 'Please press 1 to confirm, 2 to cancel or 3 to reschedule.'
                )
            )
        );

        Schema::create('voices', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('message_id')->unsigned();
            $table->foreign('message_id')->references('id')->on('messages');

            $table->string('phone_number');
            $table->string('direction')->default("outbound");

            $table->boolean('fulfilled')->default(false);
            $table->string('external_id')->nullable();

            $table->string('status')->default('new');
            $table->timestamp("queued")->nullable();
            $table->timestamp("initiated")->nullable();
            $table->timestamp("ringing")->nullable();
            $table->timestamp("answered")->nullable();
            $table->timestamp("in-progress")->nullable();
            $table->timestamp("completed")->nullable();
            $table->timestamp("busy")->nullable();
            $table->timestamp("failed")->nullable();
            $table->timestamp("no-answer")->nullable();
            $table->timestamp("canceled")->nullable();

            $table->integer("duration")->nullable();
            $table->string('script')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sms', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('message_id')->unsigned();
            $table->foreign('message_id')->references('id')->on('messages');

            $table->string('to_number');
            $table->string('from_number')->default("17272034587");
            $table->string('direction')->default("outbound");
            $table->string('body');

            $table->boolean('fulfilled')->default(false);

            $table->string('external_id')->nullable();
            $table->integer('units')->default(1);

            $table->string('status')->default('new');
            $table->timestamp('accepted')->nullable();
            $table->timestamp('queued')->nullable();
            $table->timestamp('sending')->nullable();
            $table->timestamp('sent')->nullable();
            $table->timestamp('receiving')->nullable();
            $table->timestamp('received')->nullable();
            $table->timestamp('delivered')->nullable();
            $table->timestamp('undelivered')->nullable();
            $table->timestamp('failed')->nullable();

            $table->string('error_code')->nullable();
            $table->string('error_message')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sms_templates', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->integer('type_id')->unsigned();
            $table->foreign('type_id')->references('id')->on('types');

            $table->string('template');
            $table->string('parameters');

            $table->string('confirmation')->default("yes");
            $table->string('cancellation')->default("no");
            $table->string('other')->nullable();

            $table->timestamps();
        });

        DB::table('sms_templates')->insert(
            array(
                'company_id' => 1,
                'type_id' => 2,
                'template' => 'Hello {first_name}. You have an appointment scheduled on {day_of_week} at {time_of_day}. Please respond yes to confirm or no to cancel.',
                'parameters' => json_encode(['first_name' => 'Tyler', 'day_of_week' => 'Fri.', 'time_of_day' => '1:00pm']),
                'confirmation' => 'yes',
                'cancellation' => 'no'
            )
        );

        Schema::create('emails', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('message_id')->unsigned();
            $table->foreign('message_id')->references('id')->on('messages');

            $table->string('email_address');
            $table->string('direction')->default("outbound");

            $table->boolean('fulfilled')->default(false);

            $table->string('status')->nullable();
            $table->string('external_id')->nullable();
            $table->integer('opens')->default(0);
            $table->integer('clicks')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('email_templates', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->integer('type_id')->unsigned();
            $table->foreign('type_id')->references('id')->on('types');

            $table->string('template');
            $table->string('parameters');
            $table->string('from_address');
            $table->string('from_name');
            $table->string('subject');

            $table->boolean('confirmation')->default(true);
            $table->boolean('cancellation')->default(true);
            $table->boolean('other')->default(false);

            $table->timestamps();
        });

        DB::table('email_templates')->insert(
            array(
                'company_id' => 1,
                'type_id' => 2,
                'template' => 'Defaults.Appointment',
                'from_address' => 'support@notiifii.com',
                'from_name' => 'Support Team',
                'subject' => 'Appointment Reminder',
                'parameters' => json_encode(['first_name' => 'Tyler', 'day_of_week' => 'Fri.', 'time_of_day' => '1:00pm']),
                'confirmation' => true,
                'cancellation' => true,
                'other' => true
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('email_templates');
        Schema::drop('emails');
        Schema::drop('sms_templates');
        Schema::drop('sms');
        Schema::drop('voices');
        Schema::drop('messages');
        Schema::drop('contacts');
        Schema::drop('types');
        Schema::drop('statuses');
        Schema::drop('channels');
    }
}
