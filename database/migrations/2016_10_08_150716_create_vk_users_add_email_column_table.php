<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVkUsersAddEmailColumnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vk_users', function (Blueprint $table) {
            // Email-адрес пользователя.
            $table->string('email')->nullable()->after('user_vk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vk_users', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
}
