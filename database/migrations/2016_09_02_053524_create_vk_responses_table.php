<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVkResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vk_responses', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id');
            $table->string('method');
            $table->string('access_token');
            $table->json('context');

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
        Schema::drop('vk_responses');
    }
}
