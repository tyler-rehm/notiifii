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
        Schema::create('statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('display_name');
            $table->string('description');
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('display_name');
            $table->string('description');
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('channels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('display_name');
            $table->string('description');
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid();

            $table->integer('status_id')->unsigned();
            $table->foreign('status_id')->references('id')->on('statuses');
            $table->integer('event_id')->unsigned();
            $table->foreign('event_id')->references('id')->on('events');

            $table->boolean('fulfilled')->default(false);
            $table->integer('channel_id')->unsigned();
            $table->foreign('channel_id')->references('id')->on('channels');
            $table->timestamps();
        });

        Schema::create('voices', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('message_id')->unsigned();
            $table->foreign('message_id')->references('id')->on('messages');

            $table->string('phone_number');
            $table->string('direction')->default("outbound");

            $table->boolean('fulfilled')->default(false);
            $table->timestamps();
        });

        Schema::create('sms', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('message_id')->unsigned();
            $table->foreign('message_id')->references('id')->on('messages');

            $table->string('phone_number');
            $table->string('direction')->default("outbound");

            $table->boolean('fulfilled')->default(false);
            $table->timestamps();
        });

        Schema::create('emails', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('message_id')->unsigned();
            $table->foreign('message_id')->references('id')->on('messages');

            $table->string('email_address');
            $table->string('direction')->default("outbound");

            $table->boolean('fulfilled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('messages');
    }
}
