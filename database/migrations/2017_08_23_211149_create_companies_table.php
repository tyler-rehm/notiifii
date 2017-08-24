<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('display_name');
            $table->string('description');
            $table->boolean('enabled')->default(true);

            $table->boolean('enabled_sms')->default(true);
            $table->boolean('enabled_voice')->default(true);
            $table->boolean('enabled_email')->default(true);

            $table->uuid("access_token");

            $table->timestamps();
        });

        DB::table('companies')->insert(
            array(
                'display_name' => 'Hackrforce Technologies, LLC',
                'description' => '',
                'access_token' => 'e5RTUstncNz499sX'
            )
        );

        Schema::create('company_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });

        DB::table('company_user')->insert(
            array(
                'company_id' => 1,
                'user_id' => 1
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
        Schema::drop('company_user');
        Schema::drop('companies');
    }
}
