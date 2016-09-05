<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVkUsersAddParamsColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vk_users', function (Blueprint $table) {
            /**
             * @see https://vk.com/dev/users.get
             */
            // Имя пользователя.
            $table->string('first_name')->nullable()->after('user_vk');
            // Фамилия пользователя.
            $table->string('last_name')->nullable()->after('first_name');
            /**
             * Имя пользователя в заданном падеже.
             *
             * Запрос по `first_name_{case}`.
             *
             *  nom — именительный;
             *  gen — родительный;
             *  dat — дательный;
             *  acc — винительный;
             *  ins — творительный;
             *  abl — предложный.
             *
             * @see https://vk.com/dev/fields
             */
            $table->json('first_name_case')->nullable()->after('last_name');
            /**
             * Фамилия пользователя в заданном падеже.
             *
             * Запрос по `last_name_{case}`.
             *
             *  nom — именительный;
             *  gen — родительный;
             *  dat — дательный;
             *  acc — винительный;
             *  ins — творительный;
             *  abl — предложный.
             *
             * @see https://vk.com/dev/fields
             */
            $table->json('last_name_case')->nullable()->after('first_name_case');
            // Фотография пользователя.
            $table->string('photo')->nullable()->after('last_name_case');
            // Проверка деактивации учетной записи.
            $table->tinyInteger('is_deactivated')->default(false)->after('photo');
            /**
             * @see https://vk.com/dev/account.getInfo
             */
            // Язык интерфейса. По-умолчанию, "3" - "Английский".
            $table->integer('lang')->default(3)->after('photo');
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
            $table->dropColumn('first_name', 'last_name', 'first_name_case', 'last_name_case', 'photo', 'is_deactivated', 'lang');
        });
    }
}
