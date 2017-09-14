<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVariablesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_variables', function(Blueprint $table){
          $table->increments('id');
          $table->string('key');
          $table->string('value');
          $table->boolean('hidden')->default(false);
          $table->timestamps();
        });

        Schema::create('script_variables', function(Blueprint $table){
          $table->increments('id');

          $table->integer('voice_script_part_id')->unsigned();
          $table->foreign('voice_script_part_id')->references('id')->on('voice_script_parts');

          $table->string('key');
          $table->string('value');
          $table->string('yield');
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
        //
    }
}
